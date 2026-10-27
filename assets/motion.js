import {
  animate,
  inView,
  scroll,
  stagger,
  hover,
  press,
} from "https://cdn.jsdelivr.net/npm/motion@latest/+esm";

export const EASE_OUT = [0.22, 1, 0.36, 1];

export const DURATIONS = {
  micro: 0.2,
  ui: 0.35,
  entranceMin: 0.6,
  entranceMax: 0.9,
  ambientMin: 12,
  ambientMax: 20,
};

export const SPRING_TAP = {
  type: "spring",
  stiffness: 500,
  damping: 30,
};

const REDUCED_MOTION_QUERY = "(prefers-reduced-motion: reduce)";
const REVEAL_DISTANCE = 24;
const SAFETY_CLASS = "motion-fallback";

export function prefersReducedMotion() {
  return typeof window !== "undefined"
    && typeof window.matchMedia === "function"
    && window.matchMedia(REDUCED_MOTION_QUERY).matches;
}

function asElements(elements) {
  if (!elements) return [];
  if (typeof elements === "string") return [...document.querySelectorAll(elements)];
  if (elements instanceof Element) return [elements];
  return [...elements].filter(Boolean);
}

function revealValues(direction) {
  if (direction === "fade") return { opacity: [0, 1] };
  if (direction === "blade") {
    return {
      opacity: [0, 1],
      clipPath: ["inset(0 0 100% 0)", "inset(0 0 0% 0)"],
    };
  }
  return { opacity: [0, 1], y: [REVEAL_DISTANCE, 0] };
}

function revealOptions(opts = {}) {
  const options = { ...opts };
  delete options.direction;
  delete options.delay;
  delete options.duration;
  return options;
}

export function reveal(el, opts = {}) {
  if (!el) return () => {};
  const direction = opts.direction || el.dataset.reveal || "up";
  const delay = Number(opts.delay ?? el.dataset.delay ?? 0) || 0;
  const duration = Number(opts.duration) || DURATIONS.entranceMin;

  el.classList.add("active");
  if (prefersReducedMotion()) {
    el.style.opacity = "1";
    el.style.transform = "none";
    el.style.clipPath = "none";
    return () => {};
  }

  return animate(el, revealValues(direction), {
    duration,
    delay,
    ease: EASE_OUT,
    ...revealOptions(opts),
  });
}

export function revealGroup(els, opts = {}) {
  const elements = asElements(els);
  if (!elements.length) return () => {};
  const baseDelay = Number(opts.delay) || 0;
  const staggerDelay = Number(opts.stagger ?? 0.08) || 0;

  if (prefersReducedMotion()) {
    elements.forEach((el) => reveal(el, { ...opts, delay: 0 }));
    return () => {};
  }

  const nextDelay = stagger(staggerDelay, { startDelay: baseDelay });
  return elements.map((el, index) => reveal(el, {
    ...opts,
    delay: nextDelay(index, elements.length),
  }));
}

export function blade(el) {
  return reveal(el, { direction: "blade" });
}

export function drawLine(el) {
  if (!el) return () => {};
  if (prefersReducedMotion()) {
    el.style.opacity = "1";
    el.style.clipPath = "none";
    return () => {};
  }
  return animate(el, {
    opacity: [0, 1],
    clipPath: ["inset(0 100% 0 0)", "inset(0 0% 0 0)"],
  }, {
    duration: DURATIONS.entranceMin,
    ease: EASE_OUT,
  });
}

export function countUp(el, to, opts = {}) {
  if (!el) return () => {};
  const target = Number(to) || 0;
  const formatter = opts.format || ((value) => Math.round(value).toLocaleString("en-IN"));
  if (prefersReducedMotion()) {
    el.textContent = formatter(target);
    return () => {};
  }

  const state = { value: 0 };
  return animate(state, { value: target }, {
    duration: Number(opts.duration) || DURATIONS.entranceMax,
    ease: EASE_OUT,
    onUpdate: (latest) => { el.textContent = formatter(latest.value); },
  });
}

export function magneticLift(el) {
  if (!el || prefersReducedMotion()) return () => {};
  return hover(el, (target) => {
    const enter = animate(target, { y: -2 }, { duration: DURATIONS.micro, ease: EASE_OUT });
    return () => enter.stop();
  });
}

function addTactilePress(el) {
  if (!el || prefersReducedMotion()) return () => {};
  return press(el, (target) => {
    const down = animate(target, { scale: 0.98 }, SPRING_TAP);
    return () => {
      down.stop();
      animate(target, { scale: 1 }, SPRING_TAP);
    };
  });
}

function initScrollChrome() {
  const root = document.documentElement;
  const header = document.getElementById("siteHeader");
  let progressLine = document.getElementById("dkScrollProgress");
  if (!progressLine) {
    progressLine = document.createElement("div");
    progressLine.id = "dkScrollProgress";
    progressLine.setAttribute("aria-hidden", "true");
    document.body.prepend(progressLine);
  }

  return scroll((progress) => {
    const value = Math.max(0, Math.min(1, progress));
    progressLine.style.transform = `scaleX(${value})`;
    root.style.setProperty("--dk-scroll-progress", value.toFixed(4));
    if (!header || prefersReducedMotion()) return;

    const isDesktop = window.matchMedia("(min-width: 861px)").matches;
    const headerHeight = isDesktop ? 76 - (16 * value) : 76;
    const blur = 8 + (6 * value);
    const shadow = (0.35 * value).toFixed(3);
    header.style.setProperty("--dk-header-height", `${headerHeight.toFixed(2)}px`);
    header.style.setProperty("--dk-header-blur", `${blur.toFixed(2)}px`);
    header.style.setProperty("--dk-header-shadow", shadow);
  });
}

function initDesktopUnderline() {
  const nav = document.getElementById("navLinks");
  if (!nav || prefersReducedMotion()) return () => {};
  const links = [...nav.querySelectorAll(":scope > li > .nav-link")];
  if (!links.length) return () => {};
  let underline = nav.querySelector(":scope .nav-underline");
  if (!underline) {
    underline = document.createElement("span");
    underline.className = "nav-underline";
    underline.setAttribute("aria-hidden", "true");
  }
  const active = links.find((link) => link.classList.contains("active")) || links[0];
  let focused = false;

  const moveTo = (link) => {
    if (window.matchMedia("(max-width: 860px)").matches || !link) return;
    if (underline.parentElement !== link) link.appendChild(underline);
  };
  const restore = () => {
    if (!focused) moveTo(active);
  };

  moveTo(active);
  const cleanups = links.map((link) => hover(link, () => {
    moveTo(link);
    return restore;
  }));
  links.forEach((link) => {
    link.addEventListener("focusin", () => { focused = true; moveTo(link); });
    link.addEventListener("focusout", (event) => {
      if (!nav.contains(event.relatedTarget)) {
        focused = false;
        restore();
      }
    });
  });
  nav.addEventListener("mouseleave", restore);
  return () => cleanups.forEach((stop) => stop());
}

function initBookButtonSheen() {
  if (prefersReducedMotion()) return () => {};
  const cleanups = [];
  document.querySelectorAll(".header-book-btn").forEach((button) => {
    const sheen = document.createElement("span");
    sheen.className = "dk-motion-sheen";
    sheen.setAttribute("aria-hidden", "true");
    button.appendChild(sheen);
    cleanups.push(hover(button, (target) => {
      const sweep = animate(sheen, { x: ["-130%", "130%"] }, {
        duration: 0.55,
        ease: EASE_OUT,
      });
      return () => sweep.stop();
    }));
  });
  return () => cleanups.forEach((stop) => stop());
}

function initMobileMenuMotion() {
  const button = document.getElementById("mobileMenuBtn");
  const nav = document.getElementById("navLinks");
  if (!button || !nav || prefersReducedMotion()) return () => {};
  const isMobile = () => window.matchMedia("(max-width: 860px)").matches;

  const svgLines = [...button.querySelectorAll("line")];
  svgLines.forEach((line) => {
    line.style.transformOrigin = "12px 12px";
  });
  const items = [...nav.querySelectorAll(":scope > li")];
  const observe = new MutationObserver(() => {
    if (!isMobile()) return;
    const open = nav.classList.contains("show");
    button.classList.toggle("is-open", open);
    if (open) {
      animate(nav, { opacity: [0, 1], clipPath: ["inset(0 0 100% 0)", "inset(0 0 0% 0)"] }, {
        duration: DURATIONS.ui,
        ease: EASE_OUT,
      });
      const step = window.matchMedia("(max-width: 480px)").matches ? 0.06 : 0.08;
      animate(items, { opacity: [0, 1], x: [-10, 0] }, {
        duration: DURATIONS.ui,
        delay: stagger(step),
        ease: EASE_OUT,
      });
      animate(svgLines[0], { y: [0, 6], rotate: [0, 45] }, SPRING_TAP);
      animate(svgLines[1], { opacity: [1, 0] }, { duration: DURATIONS.micro, ease: EASE_OUT });
      animate(svgLines[2], { y: [0, -6], rotate: [0, -45] }, SPRING_TAP);
    } else {
      animate(nav, { opacity: 0, clipPath: "inset(0 0 100% 0)" }, { duration: DURATIONS.micro, ease: EASE_OUT });
      animate(svgLines[0], { y: 0, rotate: 0 }, SPRING_TAP);
      animate(svgLines[1], { opacity: 1 }, { duration: DURATIONS.micro, ease: EASE_OUT });
      animate(svgLines[2], { y: 0, rotate: 0 }, SPRING_TAP);
    }
  });
  if (isMobile()) {
    nav.style.clipPath = "inset(0 0 100% 0)";
    nav.style.opacity = "0";
  }
  observe.observe(nav, { attributes: true, attributeFilter: ["class"] });
  return () => observe.disconnect();
}

function initProfileDropdownMotion() {
  const menu = document.getElementById("dkProfileMenu");
  const wrap = menu?.closest(".dk-profile-wrap");
  if (!menu || !wrap || prefersReducedMotion()) return () => {};
  const observe = new MutationObserver(() => {
    if (menu.classList.contains("open")) {
      animate(menu, { opacity: [0, 1], y: [8, 0] }, { duration: DURATIONS.ui, ease: EASE_OUT });
    }
  });
  observe.observe(menu, { attributes: true, attributeFilter: ["class"] });
  return () => observe.disconnect();
}

function initFooterMotion() {
  const footer = document.querySelector(".dk-footer");
  if (!footer) return () => {};
  const columns = [...footer.querySelectorAll(".dk-ft-col")];
  const links = [...footer.querySelectorAll(".dk-ft-links a, .dk-ft-contact a")];
  const cleanups = [];

  if (!prefersReducedMotion()) {
    cleanups.push(inView(footer, () => {
      const step = window.matchMedia("(max-width: 560px)").matches ? 0.06 : 0.08;
      const animation = animate(columns, { opacity: [0, 1], y: [16, 0] }, {
        duration: DURATIONS.entranceMin,
        delay: stagger(step),
        ease: EASE_OUT,
      });
      return () => animation.stop();
    }, { amount: 0.2 }));

    links.forEach((link) => {
      cleanups.push(hover(link, (target) => {
        target.classList.add("is-motion-hovered");
        return () => target.classList.remove("is-motion-hovered");
      }));
      link.addEventListener("focusin", () => link.classList.add("is-motion-hovered"));
      link.addEventListener("focusout", () => link.classList.remove("is-motion-hovered"));
    });
  }

  footer.querySelectorAll(".dk-ft-form button").forEach((button) => {
    let previous = button.textContent;
    const observer = new MutationObserver(() => {
      if (button.textContent === previous) return;
      previous = button.textContent;
      if (prefersReducedMotion()) {
        button.style.opacity = "1";
        return;
      }
      animate(button, { opacity: [1, 0, 1] }, { duration: DURATIONS.ui, ease: EASE_OUT });
    });
    observer.observe(button, { childList: true, characterData: true, subtree: true });
    cleanups.push(() => observer.disconnect());
  });
  return () => cleanups.forEach((stop) => typeof stop === "function" && stop());
}

function splitHomeTitle(title) {
  const text = title.textContent.trim();
  const words = text.split(/\s+/);
  title.setAttribute("aria-label", text);
  title.textContent = "";
  const wordNodes = words.map((word, index) => {
    const node = document.createElement("span");
    node.className = "home-title-word";
    node.textContent = word;
    if (index < words.length - 1) node.style.marginRight = "0.25em";
    title.appendChild(node);
    return node;
  });
  const lines = [];
  wordNodes.forEach((word) => {
    const current = lines[lines.length - 1];
    if (!current || word.offsetTop !== current[0].offsetTop) lines.push([word]);
    else current.push(word);
  });
  title.textContent = "";
  return lines.map((wordsInLine) => {
    const line = document.createElement("span");
    line.className = "home-title-line";
    wordsInLine.forEach((word) => line.appendChild(word));
    title.appendChild(line);
    return line;
  });
}

function initHomeMotion() {
  if (document.body?.dataset.motionPage !== "home") return () => {};
  const cleanups = [];
  const hero = document.querySelector("[data-home-hero]");
  const title = hero?.querySelector(".hero-title");
  const bg = hero?.querySelector(".hero-bg");
  const vignette = hero?.querySelector(".hero-vignette");

  if (hero && title && bg && vignette) {
    if (!prefersReducedMotion()) {
      const ambient = animate([
        [bg, { scale: [1, 1.06] }],
        [vignette, { opacity: [0.18, 0.3] }],
      ], {
        duration: DURATIONS.ambientMax,
        repeat: Infinity,
        repeatType: "reverse",
        ease: "easeInOut",
      });
      const parallax = animate(bg, { y: ["-2%", "2%"] }, { ease: "linear" });
      cleanups.push(() => ambient.stop(), () => parallax.stop(), scroll(parallax, {
        target: hero,
        offset: ["start end", "end start"],
      }));
    }

    cleanups.push(inView(hero, () => {
      if (prefersReducedMotion()) {
        const rule = hero.querySelector(".hero-title-rule");
        if (rule) {
          rule.style.opacity = "1";
          rule.style.transform = "none";
        }
        return;
      }
      const lines = splitHomeTitle(title);
      const rule = hero.querySelector(".hero-title-rule");
      const tagline = hero.querySelector(".hero-tagline");
      const ctas = [...hero.querySelectorAll(".hero-ctas > *")];
      const titleAnimation = animate(lines, {
        opacity: [0, 1],
        clipPath: ["inset(0 0 100% 0)", "inset(0 0 0% 0)"],
      }, {
        duration: 0.42,
        delay: stagger(0.05),
        ease: EASE_OUT,
      });
      animate(rule, { opacity: [0, 1], scaleX: [0, 1] }, {
        duration: 0.35,
        delay: 0.14,
        ease: EASE_OUT,
      });
      animate(tagline, { opacity: [0, 1], y: [12, 0] }, {
        duration: 0.3,
        delay: 0.16,
        ease: EASE_OUT,
      });
      animate(ctas, { opacity: [0, 1], y: [12, 0] }, {
        duration: 0.3,
        delay: stagger(0.06, { startDelay: 0.24 }),
        ease: EASE_OUT,
      });
      return () => titleAnimation.stop();
    }, { amount: 0.2 }));
  }

  const headers = [...document.querySelectorAll(".category-header")];
  headers.forEach((header) => {
    const eyebrow = header.querySelector(".eyebrow-tag");
    const hairline = header.querySelector(".hairline");
    if (!eyebrow || !hairline) return;
    if (!prefersReducedMotion()) {
      eyebrow.style.opacity = "0";
      eyebrow.style.clipPath = "inset(0 100% 0 0)";
      hairline.style.opacity = "0";
      hairline.style.transform = "scaleX(0)";
      hairline.style.transformOrigin = "left center";
    }
    cleanups.push(inView(header, () => {
      if (prefersReducedMotion()) return;
      animate(eyebrow, { opacity: [0, 1], clipPath: ["inset(0 100% 0 0)", "inset(0 0% 0 0)"] }, {
        duration: DURATIONS.ui,
        ease: EASE_OUT,
      });
      animate(hairline, { opacity: [0, 1], scaleX: [0, 1] }, {
        duration: DURATIONS.ui,
        delay: 0.08,
        ease: EASE_OUT,
      });
    }, { amount: 0.2 }));
  });

  const highlightGrid = document.querySelector(".highlights-grid");
  if (highlightGrid) {
    const cards = [...highlightGrid.querySelectorAll(".highlight-card")];
    cleanups.push(inView(highlightGrid, () => {
      revealGroup(cards, { stagger: 0.08, duration: DURATIONS.entranceMin });
      if (!prefersReducedMotion()) {
        animate(cards.map((card) => card.querySelector(".highlight-number")), {
          opacity: [0, 1],
          y: [12, 0],
        }, { duration: DURATIONS.ui, delay: stagger(0.06), ease: EASE_OUT });
      }
    }, { amount: 0.2 }));
    cards.forEach((card) => {
      if (prefersReducedMotion()) return;
      cleanups.push(hover(card, (target) => {
        const lift = animate(target, { y: -4, filter: "drop-shadow(0 8px 16px rgba(201,166,107,.16))" }, {
          duration: DURATIONS.micro,
          ease: EASE_OUT,
        });
        return () => {
          lift.stop();
          animate(target, { y: 0, filter: "none" }, { duration: DURATIONS.micro, ease: EASE_OUT });
        };
      }));
      cleanups.push(press(card, (target) => {
        const down = animate(target, { scale: 0.985 }, SPRING_TAP);
        return () => {
          down.stop();
          animate(target, { scale: 1 }, SPRING_TAP);
        };
      }));
    });
  }

  const servicesGrid = document.querySelector(".services-grid");
  if (servicesGrid) {
    const cards = [...servicesGrid.querySelectorAll(".service-card")];
    cards.forEach((card) => {
      const sweep = document.createElement("span");
      sweep.className = "home-service-sweep";
      sweep.setAttribute("aria-hidden", "true");
      card.appendChild(sweep);
      if (prefersReducedMotion()) {
        sweep.style.opacity = "1";
        sweep.style.transform = "none";
      }
      if (!prefersReducedMotion()) cleanups.push(hover(card, () => {
        const motion = animate(sweep, { opacity: [0.2, 1], scaleX: [0, 1] }, {
          duration: DURATIONS.ui,
          ease: EASE_OUT,
        });
        return () => {
          motion.stop();
          animate(sweep, { opacity: 0, scaleX: 0 }, { duration: DURATIONS.micro, ease: EASE_OUT });
        };
      }));
    });
    cleanups.push(inView(servicesGrid, () => {
      revealGroup(cards, { stagger: 0.08, duration: DURATIONS.entranceMin });
    }, { amount: 0.2 }));
  }

  const reviewsGrid = document.querySelector(".reviews-grid");
  if (reviewsGrid) {
    const cards = [...reviewsGrid.querySelectorAll(".review-card")];
    cards.forEach((card) => {
      const stars = card.querySelector(".star-rating");
      if (stars && !prefersReducedMotion()) {
        const text = stars.textContent.trim();
        stars.textContent = "";
        const starNodes = [...text].map((star) => {
          const node = document.createElement("span");
          node.className = "home-motion-star";
          node.textContent = star;
          stars.appendChild(node);
          return node;
        });
        card._homeStarNodes = starNodes;
      }
    });
    cleanups.push(inView(reviewsGrid, () => {
      revealGroup(cards, { stagger: 0.08, duration: DURATIONS.entranceMin });
      cards.forEach((card) => {
        const stars = card._homeStarNodes || [];
        const avatar = card.querySelector(".avatar-circle");
        if (stars.length) animate(stars, { opacity: [0, 1], y: [6, 0] }, {
          duration: DURATIONS.micro,
          delay: stagger(0.06),
          ease: EASE_OUT,
        });
        if (avatar && !prefersReducedMotion()) animate(avatar, {
          opacity: [0, 1],
          scale: [0.94, 1],
        }, { duration: DURATIONS.ui, ease: EASE_OUT });
      });
    }, { amount: 0.2 }));
  }

  return () => cleanups.forEach((stop) => typeof stop === "function" && stop());
}

function initServicesMotion() {
  if (document.body?.dataset.motionPage !== "services") return () => {};
  const cleanups = [];
  const reduced = prefersReducedMotion();

  const playMemberPrice = (price, replay = false) => {
    const oldPrice = price.querySelector(".dk-price-old");
    const newPrice = price.querySelector(".dk-price-new");
    if (!oldPrice || !newPrice || reduced) return;
    const line = oldPrice.querySelector(".motion-price-old-line");
    if (!line) return;
    line.style.transform = "scaleX(0)";
    line.style.opacity = "0";
    newPrice.style.opacity = replay ? "0.82" : "0";
    animate(line, { opacity: [0, 1], scaleX: [0, 1] }, {
      duration: DURATIONS.micro,
      ease: EASE_OUT,
    });
    animate(newPrice, {
      opacity: [replay ? 0.82 : 0, 1],
      filter: ["drop-shadow(0 0 5px rgba(201,166,107,.42))", "none"],
    }, {
      duration: DURATIONS.ui,
      delay: 0.1,
      ease: EASE_OUT,
    });
  };

  const prepareMemberPrice = (price) => {
    const oldPrice = price.querySelector(".dk-price-old");
    const newPrice = price.querySelector(".dk-price-new");
    if (!oldPrice || !newPrice || oldPrice.dataset.motionReady === "true") return;
    oldPrice.dataset.motionReady = "true";
    oldPrice.classList.add("motion-price-old");
    const line = document.createElement("span");
    line.className = "motion-price-old-line";
    line.setAttribute("aria-hidden", "true");
    oldPrice.appendChild(line);
    price._motionPlayMemberPrice = playMemberPrice;
    if (reduced) {
      newPrice.style.opacity = "1";
      return;
    }
    playMemberPrice(price);
    cleanups.push(hover(price.closest(".service-card") || price, () => {
      playMemberPrice(price, true);
      return () => {};
    }));
  };

  const prepareAllPrices = () => {
    document.querySelectorAll(".service-price").forEach(prepareMemberPrice);
  };
  prepareAllPrices();
  const priceObserver = new MutationObserver(prepareAllPrices);
  priceObserver.observe(document.body, { childList: true, subtree: true });
  cleanups.push(() => priceObserver.disconnect());

  const sections = [...document.querySelectorAll("section[data-category]")];
  sections.forEach((section) => {
    const cards = [...section.querySelectorAll(".service-card")];
    cards.forEach((card) => {
      const title = card.querySelector(".service-title");
      if (title && !reduced) {
        cleanups.push(hover(title, (target) => {
          const motion = animate(target, { x: 4 }, {
            duration: DURATIONS.micro,
            ease: EASE_OUT,
          });
          return () => {
            motion.stop();
            animate(target, { x: 0 }, { duration: DURATIONS.micro, ease: EASE_OUT });
          };
        }));
      }
    });
    cleanups.push(inView(section, () => {
      reveal(section, { direction: "up", duration: DURATIONS.entranceMin });
      revealGroup(cards, { stagger: 0.06, duration: DURATIONS.entranceMin });
      prepareAllPrices();
    }, { amount: 0.12 }));
  });

  return () => cleanups.forEach((stop) => typeof stop === "function" && stop());
}

function initBookingMotion() {
  if (document.body?.dataset.motionPage !== "booking") return () => {};
  const cleanups = [];
  const reduced = prefersReducedMotion();

  const step1 = document.getElementById("stepIndicator1");
  const step2 = document.getElementById("stepIndicator2");
  const stepLine = document.querySelector(".checkout-step-line");
  if (step1 && step2 && stepLine) {
    const progress = document.createElement("span");
    progress.className = "checkout-step-progress";
    progress.setAttribute("aria-hidden", "true");
    stepLine.appendChild(progress);

    [step1, step2].forEach((step) => {
      const number = step.querySelector(".checkout-step-num");
      if (!number) return;
      const check = document.createElement("span");
      check.className = "checkout-step-check";
      check.setAttribute("aria-hidden", "true");
      check.innerHTML = '<svg viewBox="0 0 24 24" fill="none"><polyline pathLength="1" points="5 12 10 17 19 7"></polyline></svg>';
      number.appendChild(check);
    });

    const updateSteps = () => {
      const done = step1.classList.contains("done");
      const step2Done = step2.classList.contains("done");
      const checks = [step1, step2].map((step) => step.querySelector(".checkout-step-check"));
      if (reduced) {
        progress.style.transform = done ? "scaleX(1)" : "scaleX(0)";
        checks.forEach((check, index) => {
          if (check) {
            check.style.opacity = ((index === 0 && done) || (index === 1 && step2Done)) ? "1" : "0";
            check.style.transform = "none";
          }
        });
        return;
      }
      animate(progress, { scaleX: done ? 1 : 0 }, { duration: DURATIONS.ui, ease: EASE_OUT });
      checks.forEach((check, index) => {
        if (!check) return;
        const isDone = index === 0 ? done : step2Done;
        const path = check.querySelector("polyline");
        animate(check, { opacity: isDone ? 1 : 0, scale: isDone ? 1 : 0.7 }, {
          duration: DURATIONS.ui,
          ease: EASE_OUT,
        });
        if (path) animate(path, { strokeDashoffset: isDone ? [1, 0] : [0, 1] }, {
          duration: DURATIONS.ui,
          ease: EASE_OUT,
        });
      });
    };
    updateSteps();
    const stepObserver = new MutationObserver(updateSteps);
    stepObserver.observe(step1, { attributes: true, attributeFilter: ["class"] });
    stepObserver.observe(step2, { attributes: true, attributeFilter: ["class"] });
    cleanups.push(() => stepObserver.disconnect());
  }

  const panel1 = document.getElementById("step1Panel");
  const panel2 = document.getElementById("step2Panel");
  let currentPanel = panel1 && panel1.style.display !== "none" ? panel1 : panel2;
  const animatePanelChange = () => {
    const nextPanel = [panel1, panel2].find((panel) => panel && panel.style.display !== "none");
    if (!nextPanel || nextPanel === currentPanel) return;
    const direction = nextPanel === panel2 ? 1 : -1;
    currentPanel = nextPanel;
    nextPanel.classList.add("booking-motion-panel");
    if (reduced) return;
    animate(nextPanel, {
      opacity: [0, 1],
      x: [direction * 16, 0],
      clipPath: [direction > 0 ? "inset(0 0 0 8%)" : "inset(0 8% 0 0)", "inset(0 0% 0 0)"],
    }, {
      duration: DURATIONS.ui,
      ease: EASE_OUT,
    });
  };
  if (panel1 && panel2) {
    const onPanelTrigger = (event) => {
      const trigger = event.target.closest(".btn-continue, .booking-summary-edit");
      if (!trigger) return;
      window.requestAnimationFrame(animatePanelChange);
    };
    document.addEventListener("click", onPanelTrigger, true);
    cleanups.push(() => document.removeEventListener("click", onPanelTrigger, true));
  }

  const revealSlots = () => {
    const slots = [...document.querySelectorAll(".rp-slot")]
      .filter((slot) => !slot.closest("#rpWrap"));
    if (!slots.length) return;
    const available = slots.filter((slot) => !slot.matches(".unavailable, .disabled, [aria-disabled='true']"));
    const selected = available.filter((slot) => slot.matches(".selected, .active, [aria-selected='true'], [data-selected='true']"));
    if (reduced) return;
    animate(available, { opacity: [0, 1], y: [8, 0] }, {
      duration: DURATIONS.ui,
      delay: stagger(0.06),
      ease: EASE_OUT,
    });
    selected.forEach((slot) => {
      slot.classList.add("booking-slot-motion-selected");
      animate(slot, { opacity: [0.8, 1], scale: [0.96, 1] }, {
        duration: DURATIONS.ui,
        ease: EASE_OUT,
      });
    });
  };
  const dateInput = document.getElementById("bDate");
  if (dateInput) {
    dateInput.addEventListener("change", revealSlots);
    cleanups.push(() => dateInput.removeEventListener("change", revealSlots));
  }

  const errorObserver = new MutationObserver((mutations) => {
    mutations.forEach(({ target }) => {
      if (!(target instanceof Element)) return;
      if (target.classList.contains("field-error") && target.classList.contains("show")) {
        target.classList.add("booking-motion-error");
        if (!reduced) animate(target, { opacity: [0, 1], y: [-4, 0] }, {
          duration: DURATIONS.micro,
          ease: EASE_OUT,
        });
      }
      if (target.matches(".form-input.invalid") && !reduced) {
        animate(target, { x: [0, -4, 4, -4, 4, 0] }, {
          duration: 0.3,
          ease: EASE_OUT,
        });
      }
    });
  });
  errorObserver.observe(document.body, { attributes: true, attributeFilter: ["class"], subtree: true });
  cleanups.push(() => errorObserver.disconnect());

  const amountDisplay = document.getElementById("payAmountDisplay");
  if (amountDisplay) {
    let lastHTML = amountDisplay.innerHTML;
    let lastAmount = 0;
    let amountAnimating = false;
    let amountAnimation = null;
    let internalAmountWrite = false;
    const formatAmount = (value) => `₹${Math.round(value).toLocaleString("en-IN")}`;
    const readTarget = (html) => {
      const scratch = document.createElement("span");
      scratch.innerHTML = html;
      const newPrice = scratch.querySelector(".dk-price-new");
      const source = newPrice ? newPrice.textContent : scratch.textContent;
      const match = source.replace(/,/g, "").match(/(\d+)/g);
      return match ? Number(match[match.length - 1]) : null;
    };
    const amountObserver = new MutationObserver(() => {
      if (internalAmountWrite || amountDisplay.innerHTML === lastHTML) return;
      const finalHTML = amountDisplay.innerHTML;
      const target = readTarget(finalHTML);
      lastHTML = finalHTML;
      if (target === null || reduced) {
        lastAmount = target ?? lastAmount;
        return;
      }
      if (amountAnimation) amountAnimation.stop();
      amountAnimating = true;
      const state = { value: lastAmount };
      internalAmountWrite = true;
      amountDisplay.textContent = formatAmount(lastAmount);
      internalAmountWrite = false;
      amountAnimation = animate(state, { value: target }, {
        duration: DURATIONS.ui,
        ease: EASE_OUT,
        onUpdate: (latest) => {
          internalAmountWrite = true;
          amountDisplay.textContent = formatAmount(latest.value);
          internalAmountWrite = false;
        },
        onComplete: () => {
          internalAmountWrite = true;
          amountDisplay.innerHTML = finalHTML;
          internalAmountWrite = false;
          lastAmount = target;
          amountAnimating = false;
          amountAnimation = null;
        },
      });
    });
    amountObserver.observe(amountDisplay, { childList: true, characterData: true, subtree: true });
    cleanups.push(() => amountObserver.disconnect());
  }

  const successIcon = document.getElementById("rpScreenIcon");
  if (successIcon) {
    const drawSuccess = () => {
      const path = successIcon.querySelector("polyline");
      if (!successIcon.classList.contains("rp-done") || !path) return;
      if (reduced) {
        path.style.strokeDashoffset = "0";
        return;
      }
      path.style.strokeDasharray = "1";
      path.style.strokeDashoffset = "1";
      path.setAttribute("pathLength", "1");
      animate(path, { strokeDashoffset: [1, 0], opacity: [0, 1] }, {
        duration: DURATIONS.entranceMin,
        ease: EASE_OUT,
      });
    };
    const successObserver = new MutationObserver(drawSuccess);
    successObserver.observe(successIcon, {
      attributes: true,
      attributeFilter: ["class"],
      childList: true,
      subtree: true,
    });
    cleanups.push(() => successObserver.disconnect());
  }

  return () => cleanups.forEach((stop) => typeof stop === "function" && stop());
}

function initMembershipMotion() {
  if (document.body?.dataset.motionPage !== "membership") return () => {};
  const cleanups = [];
  const reduced = prefersReducedMotion();
  const grid = document.querySelector(".plans-grid");
  const cards = [...document.querySelectorAll(".plan-card")];
  if (!grid || !cards.length) return () => {};

  const featureTicks = cards.flatMap((card) => [...card.querySelectorAll(".plan-features li svg")]);
  const discountTags = cards.map((card) => card.querySelector(".plan-discount-tag")).filter(Boolean);

  cleanups.push(inView(grid, () => {
    revealGroup(cards, {
      direction: "up",
      stagger: 0.1,
      duration: DURATIONS.entranceMin,
    });
    revealGroup(featureTicks, {
      direction: "fade",
      stagger: 0.06,
      delay: 0.28,
      duration: DURATIONS.micro,
    });
    if (reduced) {
      discountTags.forEach((tag) => { tag.style.transform = "none"; });
      return;
    }
    discountTags.forEach((tag, index) => {
      tag.style.transform = "scale(0.94)";
      animate(tag, { scale: [0.94, 1] }, {
        ...SPRING_TAP,
        delay: 0.38 + (index * 0.06),
      });
    });
  }, { amount: 0.15 }));

  const goldCard = cards.find((card) => card.classList.contains("gold"));
  if (goldCard) {
    const shimmer = document.createElement("span");
    shimmer.className = "membership-border-shimmer";
    shimmer.setAttribute("aria-hidden", "true");
    goldCard.appendChild(shimmer);
    cleanups.push(inView(goldCard, () => {
      if (reduced) return () => {};
      const ambient = animate(shimmer, {
        x: ["-120%", "620%"],
        opacity: [0, 0.72, 0],
        filter: ["blur(3px)", "blur(0px)", "blur(3px)"],
      }, {
        duration: 1.4,
        delay: 0.6,
        ease: EASE_OUT,
        repeat: Infinity,
        repeatType: "loop",
        repeatDelay: 4.6,
      });
      return () => ambient.stop();
    }, { amount: 0.2 }));
  }

  const shineTargets = [
    ...document.querySelectorAll(".btn-choose-plan.current"),
    ...document.querySelectorAll("#navMemberBadge .nav-member-badge"),
  ];
  shineTargets.forEach((target) => {
    target.classList.add("membership-shine-target");
    const shine = document.createElement("span");
    shine.className = "membership-badge-shine";
    shine.setAttribute("aria-hidden", "true");
    target.appendChild(shine);
    if (!reduced) {
      cleanups.push(animate(shine, {
        x: ["-120%", "420%"],
        opacity: [0, 0.7, 0],
      }, {
        duration: 1.1,
        delay: 0.8,
        ease: EASE_OUT,
      }));
    }
  });

  return () => cleanups.forEach((stop) => typeof stop === "function" && stop());
}

function initAccountMotion() {
  const page = document.body?.dataset.motionPage;
  if (!['login', 'signup', 'profile'].includes(page)) return () => {};
  const cleanups = [];
  const reduced = prefersReducedMotion();

  const formCard = document.querySelector('.form-wrap');
  if (formCard) {
    cleanups.push(inView(formCard, () => reveal(formCard, {
      direction: 'up',
      duration: DURATIONS.entranceMin,
    }), { amount: 0.2 }));
  }

  const prepareInputs = (root = document) => {
    root.querySelectorAll('input:not([type="hidden"]), textarea, select').forEach((input) => {
      if (input.closest('.account-input-shell')) return;
      const shell = document.createElement('span');
      shell.className = 'account-input-shell';
      shell.style.position = 'relative';
      shell.style.display = 'block';
      input.parentNode.insertBefore(shell, input);
      shell.appendChild(input);
      const line = document.createElement('span');
      line.className = 'account-focus-line';
      line.setAttribute('aria-hidden', 'true');
      line.style.position = 'absolute';
      line.style.left = '1px';
      line.style.right = '1px';
      line.style.bottom = '0';
      line.style.height = '2px';
      line.style.background = 'var(--primary, #C9A66B)';
      line.style.transform = 'scaleX(0)';
      line.style.transformOrigin = 'center';
      line.style.pointerEvents = 'none';
      shell.appendChild(line);
      const setFocusLine = (focused) => {
        if (reduced) {
          line.style.transform = focused ? 'scaleX(1)' : 'scaleX(0)';
          return;
        }
        animate(line, { scaleX: focused ? 1 : 0 }, {
          duration: DURATIONS.micro,
          ease: EASE_OUT,
        });
      };
      input.addEventListener('focus', () => setFocusLine(true));
      input.addEventListener('blur', () => setFocusLine(false));
    });
  };
  prepareInputs();

  const accountObserver = new MutationObserver((mutations) => {
    mutations.forEach(({ target }) => {
      if (target instanceof Element) prepareInputs(target);
    });
  });
  accountObserver.observe(document.body, { childList: true, subtree: true });
  cleanups.push(() => accountObserver.disconnect());

  const animateInvalid = (input) => {
    if (!input || reduced) return;
    animate(input, { x: [0, -4, 4, -4, 4, 0] }, {
      duration: 0.3,
      ease: EASE_OUT,
    });
  };
  const animateError = (error) => {
    if (!error || error.hidden || error.classList.contains('account-error-seen')) return;
    if (!error.matches('.field-error, .pf-error, .error')) return;
    error.classList.add('account-error-seen');
    if (!reduced) animate(error, { opacity: [0, 1], y: [-4, 0] }, {
      duration: DURATIONS.micro,
      ease: EASE_OUT,
    });
  };
  document.querySelectorAll('input.invalid, .field-error, .pf-error, .error').forEach((el) => {
    if (el.matches('input')) animateInvalid(el);
    else animateError(el);
  });
  const validationObserver = new MutationObserver((mutations) => {
    mutations.forEach(({ target }) => {
      if (!(target instanceof Element)) return;
      if (target.matches('input.invalid, .pf-input.invalid')) animateInvalid(target);
      animateError(target);
    });
  });
  validationObserver.observe(document.body, {
    attributes: true,
    attributeFilter: ['class', 'hidden'],
    subtree: true,
  });
  cleanups.push(() => validationObserver.disconnect());

  const tabLists = [...document.querySelectorAll('[role="tablist"], .account-tabs')];
  tabLists.forEach((list) => {
    const tabs = [...list.querySelectorAll('[role="tab"], .account-tab')];
    if (tabs.length < 2) return;
    list.style.position = 'relative';
    const underline = document.createElement('span');
    underline.className = 'account-tab-underline';
    underline.setAttribute('aria-hidden', 'true');
    underline.style.position = 'absolute';
    underline.style.left = '0';
    underline.style.bottom = '0';
    underline.style.height = '2px';
    underline.style.width = '0';
    underline.style.background = 'var(--primary, #C9A66B)';
    underline.style.transformOrigin = 'left center';
    list.appendChild(underline);
    const updateTab = () => {
      const active = tabs.find((tab) => tab.getAttribute('aria-selected') === 'true' || tab.classList.contains('active')) || tabs[0];
      underline.style.width = `${active.getBoundingClientRect().width}px`;
      const left = active.offsetLeft;
      if (reduced) underline.style.transform = `translateX(${left}px) scaleX(1)`;
      else animate(underline, { x: left, scaleX: 1 }, { duration: DURATIONS.ui, ease: EASE_OUT });
    };
    tabs.forEach((tab) => tab.addEventListener('click', () => window.requestAnimationFrame(updateTab)));
    updateTab();
  });

  if (page === 'profile') {
    const animateProfileContent = () => {
      document.querySelectorAll('.pf-card').forEach((card) => {
        if (card.dataset.accountMotionCard === 'true') return;
        card.dataset.accountMotionCard = 'true';
        if (reduced) return;
        animate(card, { opacity: [0, 1], y: [10, 0] }, {
          duration: DURATIONS.entranceMin,
          ease: EASE_OUT,
        });
      });
      document.querySelectorAll('.pf-appt').forEach((row) => {
        if (row.dataset.accountMotionRow === 'true') return;
        row.dataset.accountMotionRow = 'true';
        if (!reduced) animate(row, { opacity: [0, 1], y: [8, 0] }, {
          duration: DURATIONS.ui,
          ease: EASE_OUT,
        });
      });
      document.querySelectorAll('.pf-chip').forEach((badge) => {
        if (badge.dataset.accountMotionBadge === 'true') return;
        badge.dataset.accountMotionBadge = 'true';
        if (!reduced) animate(badge, { opacity: [0, 1] }, {
          duration: DURATIONS.micro,
          ease: EASE_OUT,
        });
      });
    };
    let walletValue = 0;
    let walletTarget = null;
    let walletAnimation = null;
    let walletElement = null;
    let walletWriting = false;
    const animateWallet = () => {
      const value = document.querySelector('#pfWallet .pf-stat-value');
      if (!value) return;
      if (walletAnimation && value === walletElement) return;
      if (walletAnimation) {
        walletAnimation.stop();
        walletAnimation = null;
      }
      const match = value.textContent.replace(/,/g, '').match(/-?\d+/);
      if (!match) return;
      const target = Number(match[0]);
      if (target === walletTarget) return;
      walletTarget = target;
      if (reduced) {
        walletValue = target;
        return;
      }
      if (walletAnimation) walletAnimation.stop();
      walletElement = value;
      const state = { value: walletValue };
      walletWriting = true;
      value.textContent = `₹${Math.round(walletValue).toLocaleString('en-IN')}`;
      walletWriting = false;
      walletAnimation = animate(state, { value: target }, {
        duration: DURATIONS.entranceMax,
        ease: EASE_OUT,
        onUpdate: (latest) => {
          walletWriting = true;
          value.textContent = `₹${Math.round(latest.value).toLocaleString('en-IN')}`;
          walletWriting = false;
        },
        onComplete: () => {
          walletWriting = true;
          value.textContent = `₹${target.toLocaleString('en-IN')}`;
          walletWriting = false;
          walletValue = target;
          walletAnimation = null;
        },
      });
    };
    const profileObserver = new MutationObserver(() => {
      if (!walletWriting) animateWallet();
      animateProfileContent();
    });
    const app = document.getElementById('pfApp');
    profileObserver.observe(app || document.body, { childList: true, subtree: true });
    animateProfileContent();
    animateWallet();
    cleanups.push(() => profileObserver.disconnect());
  }

  const modalState = new WeakMap();
  const animateModal = (overlay) => {
    const open = !overlay.hidden && (overlay.classList.contains('show') || getComputedStyle(overlay).display !== 'none');
    if (!open) {
      modalState.set(overlay, false);
      return;
    }
    if (modalState.get(overlay) === true) return;
    modalState.set(overlay, true);
    const panel = overlay.querySelector('.modal, .pf-modal') || overlay.firstElementChild;
    overlay.style.animation = 'none';
    if (panel) panel.style.animation = 'none';
    if (reduced) return;
    animate(overlay, { opacity: [0, 1] }, { duration: DURATIONS.ui, ease: EASE_OUT });
    if (panel) animate(panel, { opacity: [0, 1], scale: [0.96, 1] }, { duration: DURATIONS.ui, ease: EASE_OUT });
  };
  const scanModals = () => {
    document.querySelectorAll('#fpModal, .pf-overlay, body > div[style*="position:fixed"]').forEach(animateModal);
  };
  scanModals();
  const modalObserver = new MutationObserver(scanModals);
  modalObserver.observe(document.body, { attributes: true, attributeFilter: ['class', 'hidden'], childList: true, subtree: true });
  cleanups.push(() => modalObserver.disconnect());

  return () => cleanups.forEach((stop) => typeof stop === 'function' && stop());
}

function initAboutMotion() {
  if (document.body?.dataset.motionPage !== 'about') return () => {};
  const cleanups = [];
  const reduced = prefersReducedMotion();
  const story = document.querySelector('#ab-story');
  if (story) {
    const storyLines = [...story.querySelectorAll('.ab-timeline-content h3, .ab-timeline-content p')];
    cleanups.push(inView(story, () => {
      revealGroup(storyLines, { stagger: 0.08, duration: DURATIONS.entranceMin });
    }, { amount: 0.18 }));
  }

  const stats = [...document.querySelectorAll('.ab-stat > span:first-child')];
  if (stats.length) {
    cleanups.push(inView(document.querySelector('.ab-stat-strip') || stats[0], () => {
      stats.forEach((stat) => {
        const raw = stat.textContent.trim();
        const number = Number((raw.match(/\d+/) || ['0'])[0]);
        let format = (value) => `${Math.round(value)}`;
        if (raw.includes('+')) format = (value) => `${Math.round(value)}+`;
        else if (raw.includes('₹')) format = (value) => `₹${Math.round(value).toLocaleString('en-IN')}`;
        else if (raw.includes('/')) {
          const suffix = raw.slice(raw.indexOf('/'));
          format = (value) => `${Math.round(value)}${suffix}`;
        }
        countUp(stat, number, { duration: reduced ? 0 : DURATIONS.entranceMax, format });
      });
    }, { amount: 0.25 }));
  }

  const visitImage = document.querySelector('.ab-visit-image');
  if (visitImage) {
    cleanups.push(inView(visitImage, () => {
      if (reduced) return () => {};
      visitImage.style.clipPath = 'inset(0 100% 0 0)';
      visitImage.style.transform = 'scale(1.06)';
      return animate(visitImage, {
        clipPath: ['inset(0 100% 0 0)', 'inset(0 0% 0 0)'],
        scale: [1.06, 1],
      }, { duration: DURATIONS.entranceMax, ease: EASE_OUT });
    }, { amount: 0.2 }));
  }
  return () => cleanups.forEach((stop) => typeof stop === 'function' && stop());
}

function initReviewsMotion() {
  if (document.body?.dataset.motionPage !== 'reviews') return () => {};
  const cleanups = [];
  const reduced = prefersReducedMotion();
  const prepared = new WeakSet();
  const prepareCard = (card) => {
    if (!card || prepared.has(card)) return;
    prepared.add(card);
    if (!reduced) {
      cleanups.push(hover(card, (target) => {
        const lift = animate(target, { y: -4 }, { duration: DURATIONS.micro, ease: EASE_OUT });
        return () => {
          lift.stop();
          animate(target, { y: 0 }, { duration: DURATIONS.micro, ease: EASE_OUT });
        };
      }));
    }
    const fills = [...card.querySelectorAll('.rating-bar-fill, .rating-fill')];
    fills.forEach((fill) => {
      const target = Number(fill.dataset.rating || fill.getAttribute('aria-valuenow') || 100) / 100;
      if (reduced) fill.style.transform = `scaleX(${Math.max(0, Math.min(1, target))})`;
      else animate(fill, { scaleX: [0, Math.max(0, Math.min(1, target))] }, {
        duration: DURATIONS.entranceMin,
        ease: EASE_OUT,
      });
    });
    if (card.classList.contains('account-motion-new-review') && !reduced) {
      animate(card, { opacity: [0, 1], y: [12, 0] }, { duration: DURATIONS.entranceMin, ease: EASE_OUT });
    }
  };
  const grid = document.getElementById('reviewsGrid');
  if (!grid) return () => {};
  const revealCards = () => {
    const cards = [...grid.querySelectorAll('.review-card')];
    cards.forEach(prepareCard);
    if (reduced) return;
    revealGroup(cards, { stagger: 0.08, duration: DURATIONS.entranceMin });
  };
  cleanups.push(inView(grid, revealCards, { amount: 0.16 }));
  grid.querySelectorAll('.review-card').forEach(prepareCard);
  const reviewObserver = new MutationObserver(() => {
    grid.querySelectorAll('.review-card').forEach((card) => {
      const isNew = !prepared.has(card);
      prepareCard(card);
      if (isNew && !reduced) {
        animate(card, { opacity: [0, 1], y: [12, 0] }, {
          duration: DURATIONS.entranceMin,
          ease: EASE_OUT,
        });
      }
    });
  });
  reviewObserver.observe(grid, { childList: true, subtree: true });
  cleanups.push(() => reviewObserver.disconnect());
  return () => cleanups.forEach((stop) => typeof stop === 'function' && stop());
}

function initContactMotion() {
  if (document.body?.dataset.motionPage !== 'contact') return () => {};
  const cleanups = [];
  const reduced = prefersReducedMotion();
  const map = document.querySelector('.map-container');
  if (map) {
    cleanups.push(inView(map, () => {
      if (reduced) {
        map.style.opacity = '1';
        return () => {};
      }
      return animate(map, { opacity: [0, 1] }, { duration: DURATIONS.entranceMax, ease: EASE_OUT });
    }, { amount: 0.2 }));
  }
  const whatsapp = document.querySelector('.whatsapp-float');
  if (whatsapp && !reduced) {
    let timer = null;
    let pulse = null;
    const startPulse = () => {
      if (timer) clearInterval(timer);
      timer = setInterval(() => {
        pulse?.stop();
        pulse = animate(whatsapp, { scale: [1, 1.06, 1] }, {
          duration: 0.55,
          ease: EASE_OUT,
        });
      }, 8000);
    };
    startPulse();
    cleanups.push(hover(whatsapp, () => {
      if (timer) clearInterval(timer);
      pulse?.stop();
      return () => startPulse();
    }));
    cleanups.push(() => {
      if (timer) clearInterval(timer);
      pulse?.stop();
    });
  }
  return () => cleanups.forEach((stop) => typeof stop === 'function' && stop());
}

function isExcludedNavigation(link, event) {
  if (!link || event.defaultPrevented || event.button !== 0) return true;
  if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return true;
  if (link.target === "_blank" || link.hasAttribute("download")) return true;
  const raw = link.getAttribute("href") || "";
  if (!raw || raw.startsWith("#") || /^(mailto:|tel:)/i.test(raw) || /wa\.me/i.test(raw)) return true;
  let url;
  try { url = new URL(link.href, window.location.href); } catch { return true; }
  if (url.origin !== window.location.origin) return true;
  if (url.pathname === window.location.pathname && url.search === window.location.search) return true;
  return false;
}

function initInternalTransitions() {
  let transitioning = false;
  if (!prefersReducedMotion()) {
    animate(document.body, { opacity: [0.97, 1] }, {
      duration: DURATIONS.ui,
      ease: EASE_OUT,
    });
  } else {
    document.body.style.opacity = "1";
  }

  const onClick = (event) => {
    const link = event.target.closest("a");
    if (isExcludedNavigation(link, event) || transitioning) return;
    event.preventDefault();
    transitioning = true;
    if (prefersReducedMotion()) {
      window.location.assign(link.href);
      return;
    }
    animate(document.body, { opacity: [1, 0] }, {
      duration: DURATIONS.micro,
      ease: EASE_OUT,
      onComplete: () => window.location.assign(link.href),
    });
  };
  document.addEventListener("click", onClick);
  return () => document.removeEventListener("click", onClick);
}

export function initGlobalMotion() {
  if (typeof document === "undefined") return () => {};
  const cleanups = [];
  const homePage = document.body?.dataset.motionPage === "home";
  const servicesPage = document.body?.dataset.motionPage === "services";
  const bookingPage = document.body?.dataset.motionPage === "booking";
  const membershipPage = document.body?.dataset.motionPage === "membership";
  const aboutPage = document.body?.dataset.motionPage === "about";
  const reviewsPage = document.body?.dataset.motionPage === "reviews";
  const contactPage = document.body?.dataset.motionPage === "contact";
  const accountPage = ["login", "signup", "profile"].includes(document.body?.dataset.motionPage);
  if (homePage) cleanups.push(initHomeMotion());
  if (servicesPage) cleanups.push(initServicesMotion());
  if (bookingPage) cleanups.push(initBookingMotion());
  if (membershipPage) cleanups.push(initMembershipMotion());
  if (aboutPage) cleanups.push(initAboutMotion());
  if (reviewsPage) cleanups.push(initReviewsMotion());
  if (contactPage) cleanups.push(initContactMotion());
  if (accountPage) cleanups.push(initAccountMotion());

  document.querySelectorAll(".reveal").forEach((el) => {
    if (homePage && el.closest(".highlights-grid, .services-grid, .reviews-grid")) return;
    if (servicesPage && el.matches("section[data-category]")) return;
    if (membershipPage && el.matches(".plan-card")) return;
    if (reviewsPage && el.closest(".reviews-grid")) return;
    cleanups.push(inView(el, () => reveal(el, {
      direction: el.dataset.reveal || "up",
      delay: el.dataset.delay || 0,
      duration: Number(el.dataset.duration) || DURATIONS.entranceMin,
    }), { amount: 0.2 }));
  });

  if (!prefersReducedMotion()) {
    document.querySelectorAll("button, [role='button'], .btn-primary, .btn-secondary, .pf-btn, .header-book-btn, .book-mini-btn").forEach((el) => {
      cleanups.push(addTactilePress(el));
    });
  }

  cleanups.push(initScrollChrome());
  cleanups.push(initDesktopUnderline());
  cleanups.push(initBookButtonSheen());
  cleanups.push(initMobileMenuMotion());
  cleanups.push(initProfileDropdownMotion());
  cleanups.push(initFooterMotion());
  cleanups.push(initInternalTransitions());

  window.setTimeout(() => {
    document.querySelectorAll(".reveal").forEach((el) => el.classList.add("active"));
    document.documentElement.classList.add(SAFETY_CLASS);
  }, 2000);

  return () => cleanups.forEach((stop) => typeof stop === "function" && stop());
}

initGlobalMotion();
