document.addEventListener('DOMContentLoaded', () => {
    const mainContent = document.getElementById('main-content');
    document.querySelector('.bj-skip-link')?.addEventListener('click', event => {
        if (!mainContent) return;
        event.preventDefault();
        mainContent.focus({ preventScroll: true });
        mainContent.scrollIntoView({ block: 'start', behavior: 'instant' });
    });
    if (location.hash === '#main-content') {
        history.replaceState(history.state, '', location.pathname + location.search);
    }

    const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
    const gsap = window.gsap;
    const ScrollTrigger = window.ScrollTrigger;
    const page = document.body.dataset.page || '';
    const motionProfile = document.body.dataset.motionProfile || 'balanced';
    const featureEnabled = dataKey => {
        const override = document.body.dataset[dataKey];
        if (override === 'true') return true;
        if (override === 'false') return false;
        return motionProfile === 'cinematic';
    };
    const motionFeatures = Object.freeze({
        smoothScroll: featureEnabled('smoothScroll'),
        scrollParallax: featureEnabled('scrollParallax'),
        ribbonMotion: featureEnabled('ribbonMotion'),
    });
    const motionLevel = page === 'landing'
        ? 'high'
        : ['restoran', 'tiket.index'].includes(page)
            ? 'medium'
            : ['login', 'register', 'wisatawan.pemesanan.create', 'wisatawan.pemesanan.riwayat'].includes(page)
                ? 'low'
                : 'minimal';

    if (gsap && ScrollTrigger) gsap.registerPlugin(ScrollTrigger);

    const enableLenis = motionFeatures.smoothScroll
        && ['high', 'medium'].includes(motionLevel)
        && !reducedMotion.matches
        && matchMedia('(min-width: 1024px) and (hover: hover) and (pointer: fine)').matches
        && window.Lenis
        && gsap
        && ScrollTrigger;

    if (enableLenis) {
        const lenis = new window.Lenis({
            lerp: .14,
            smoothWheel: true,
            anchors: { offset: -76 },
            prevent: node => node instanceof Element && Boolean(node.closest('dialog, [data-lenis-prevent]')),
        });
        lenis.on('scroll', ScrollTrigger.update);
        gsap.ticker.add(time => lenis.raf(time * 1000));
        gsap.ticker.lagSmoothing(0);
    }

    // Animate the real ticket count once it enters view; keep its final value without JS.
    const count = document.querySelector('[data-bj-count]');
    if (count && gsap && ScrollTrigger && !reducedMotion.matches) {
        const target = Number(count.dataset.bjCount);
        if (Number.isFinite(target) && target > 0) {
            const counter = { value: 0 };
            gsap.to(counter, {
                value: target,
                duration: .9,
                ease: 'power3.out',
                snap: { value: 1 },
                onUpdate: () => { count.textContent = String(counter.value); },
                scrollTrigger: { trigger: count, start: 'top 88%', once: true },
            });
        }
    }
    document.querySelectorAll('[data-stitch-gallery]').forEach(gallery => {
        const viewport = gallery.querySelector('[data-bj-gallery-viewport]');
        const prev = gallery.querySelector('[data-stitch-prev]');
        const next = gallery.querySelector('[data-stitch-next]');
        if (!viewport || !prev || !next) return;
        const update = () => {
            prev.disabled = viewport.scrollLeft <= 1;
            next.disabled = viewport.scrollLeft + viewport.clientWidth >= viewport.scrollWidth - 1;
        };
        const move = direction => viewport.scrollBy({ left: direction * viewport.clientWidth * .8, behavior: reducedMotion.matches ? 'auto' : 'smooth' });
        prev.addEventListener('click', () => move(-1));
        next.addEventListener('click', () => move(1));
        viewport.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update, { passive: true });
        update();
    });
    const heroVideo = document.querySelector('[data-hero-video]');
    if (heroVideo) {
        const syncVideo = () => {
            if (reducedMotion.matches || !heroVideo.closest('[data-bj-hero-slide]')?.classList.contains('is-active')) heroVideo.pause();
            else heroVideo.play().catch(() => {});
        };
        reducedMotion.addEventListener('change', syncVideo);
        syncVideo();
    }

    const hero = document.querySelector('[data-bj-hero]');
    if (hero) {
        const slides = [...hero.querySelectorAll('[data-bj-hero-slide]')];
        const current = hero.querySelector('[data-bj-hero-current]');
        const caption = hero.querySelector('[data-bj-hero-caption]');
        const progress = hero.querySelector('[data-bj-hero-progress]');
        const syncHero = index => {
            slides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === index));
            if (current) current.textContent = String(index + 1).padStart(2, '0');
            if (caption) caption.textContent = slides[index]?.dataset.caption || 'Bangkiang Jaran';
            if (heroVideo) {
                if (index === 0 && !reducedMotion.matches) heroVideo.play().catch(() => {});
                else heroVideo.pause();
            }
            if (gsap && slides[index] && !reducedMotion.matches) {
                gsap.fromTo(slides[index].querySelector('img'),
                    { scale: 1.1, filter: 'brightness(.82) saturate(.9)' },
                    { scale: 1.035, filter: 'brightness(1) saturate(1)', duration: 1.1, ease: 'power2.out', overwrite: true });
            }
        };
        const swiperElement = hero.querySelector('[data-bj-hero-swiper]');

        if (swiperElement && window.Swiper) {
            new window.Swiper(swiperElement, {
                effect: 'fade',
                fadeEffect: { crossFade: true },
                speed: reducedMotion.matches ? 0 : 900,
                rewind: true,
                allowTouchMove: slides.length > 1,
                keyboard: { enabled: true, onlyInViewport: true },
                a11y: { enabled: true },
                autoplay: slides.length > 1 && !reducedMotion.matches
                    ? { delay: 6800, disableOnInteraction: false, pauseOnMouseEnter: true }
                    : false,
                navigation: {
                    prevEl: hero.querySelector('[data-bj-hero-prev]'),
                    nextEl: hero.querySelector('[data-bj-hero-next]'),
                },
                on: {
                    init: swiper => syncHero(swiper.realIndex),
                    slideChangeTransitionStart: swiper => syncHero(swiper.realIndex),
                    autoplayTimeLeft: (_swiper, _time, ratio) => {
                        if (progress) progress.style.transform = `scaleX(${1 - ratio})`;
                    },
                },
            });
        } else {
            let index = 0;
            const show = next => {
                index = (next + slides.length) % slides.length;
                syncHero(index);
            };
            hero.querySelector('[data-bj-hero-prev]')?.addEventListener('click', () => show(index - 1));
            hero.querySelector('[data-bj-hero-next]')?.addEventListener('click', () => show(index + 1));
            syncHero(index);
        }
    }

    document.querySelectorAll('[data-bj-gallery]').forEach(gallery => {
        const viewport = gallery.querySelector('[data-bj-gallery-viewport]');
        if (!viewport) return;
        const items = [...viewport.children];
        const count = gallery.querySelector('[data-bj-gallery-current]');
        const previous = gallery.querySelector('[data-bj-gallery-prev]');
        const next = gallery.querySelector('[data-bj-gallery-next]');
        const step = () => items[0] ? items[0].getBoundingClientRect().width + parseFloat(getComputedStyle(viewport).gap || 0) : 300;
        const update = () => {
            if (count) count.textContent = String(Math.min(items.length, Math.round(viewport.scrollLeft / step()) + 1)).padStart(2, '0');
            if (previous) previous.disabled = viewport.scrollLeft <= 2;
            if (next) next.disabled = viewport.scrollLeft + viewport.clientWidth >= viewport.scrollWidth - 2;
        };
        previous?.addEventListener('click', () => viewport.scrollBy({ left: -step(), behavior: reducedMotion.matches ? 'auto' : 'smooth' }));
        next?.addEventListener('click', () => viewport.scrollBy({ left: step(), behavior: reducedMotion.matches ? 'auto' : 'smooth' }));
        viewport.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update, { passive: true });
        update();
    });

    const motionRoot = document.querySelector('#main-content');
    if (motionRoot) {
        const allowAutoMotion = ['high', 'medium'].includes(motionLevel) && !document.body.classList.contains('bj-marketing');
        if (allowAutoMotion) {
            motionRoot.querySelectorAll('h1, h2').forEach(heading => {
                if (heading.closest('[data-no-motion], dialog')) return;
                heading.dataset.bjSplit = '';
                if (!heading.closest('.bj-reveal')) heading.classList.add('bj-reveal');
            });
        }

        const mediaContainers = new Set();
        if (allowAutoMotion) {
            motionRoot.querySelectorAll('img').forEach(image => {
                if (image.closest('[data-bj-hero-slide], [data-bj-gallery-track], .bj-home-ribbon, dialog')) return;
                const container = image.closest('figure, [class*="__media"], [class*="__photo"], .bj-gallery__item') || image;
                mediaContainers.add(container);
            });
        }
        mediaContainers.forEach(container => container.classList.add('bj-motion-media'));

        const groupCounts = new Map();
        motionRoot.querySelectorAll('.bj-reveal, .bj-motion-media, [data-bj-split]').forEach(item => {
            const group = item.parentElement;
            const order = groupCounts.get(group) || 0;
            item.style.setProperty('--bj-reveal-order', String(order % 6));
            groupCounts.set(group, order + 1);
        });
    }

    if (!reducedMotion.matches) {
        document.querySelectorAll('[data-bj-split]').forEach(element => {
            const label = element.textContent.replace(/\s+/g, ' ').trim();
            if (!label || element.classList.contains('bj-split-ready')) return;

            element.setAttribute('aria-label', label);
            const textNodes = [];
            const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT, {
                acceptNode: node => node.nodeValue.trim()
                    ? NodeFilter.FILTER_ACCEPT
                    : NodeFilter.FILTER_REJECT,
            });
            while (walker.nextNode()) textNodes.push(walker.currentNode);

            let wordIndex = 0;
            textNodes.forEach(node => {
                const fragment = document.createDocumentFragment();
                node.nodeValue.split(/(\s+)/).forEach(part => {
                    if (!part) return;
                    if (/^\s+$/.test(part)) {
                        fragment.appendChild(document.createTextNode(part));
                        return;
                    }
                    const outer = document.createElement('span');
                    const inner = document.createElement('span');
                    outer.className = 'bj-word';
                    outer.style.setProperty('--bj-word-index', wordIndex++);
                    outer.setAttribute('aria-hidden', 'true');
                    inner.textContent = part;
                    outer.appendChild(inner);
                    fragment.appendChild(outer);
                });
                node.replaceWith(fragment);
            });
            element.classList.add('bj-split-ready');
        });
    }

    // The long homepage gallery is pinned for a short chapter of the page.
    // Vertical scroll is mapped to opposing horizontal tracks and eased on
    // every frame so mouse wheels, touchpads, and touch scrolling feel alike.
    document.querySelectorAll('[data-bj-scroll-gallery]').forEach(section => {
        const tracks = [...section.querySelectorAll('[data-bj-gallery-track]')];
        const progressBar = section.querySelector('[data-bj-scroll-gallery-progress]');
        const stage = section.querySelector('.bj-home-scroll-gallery__stage');
        const heading = section.querySelector('.bj-home-scroll-gallery__heading');
        const wideScreen = matchMedia('(min-width: 701px)');
        let target = 0;
        let current = 0;
        let frame = 0;

        const measureProgress = () => {
            if (!wideScreen.matches || reducedMotion.matches) return 0;
            const bounds = section.getBoundingClientRect();
            const travel = Math.max(1, bounds.height - innerHeight);
            return Math.max(0, Math.min(1, -bounds.top / travel));
        };

        const render = () => {
            target = measureProgress();
            current += (target - current) * .085;

            tracks.forEach((track, index) => {
                const overflow = Math.max(track.scrollWidth - innerWidth * .74, innerWidth * .58);
                const startsRight = track.dataset.direction !== 'right';
                const from = startsRight ? innerWidth * (.42 + index * .04) : -overflow;
                const to = startsRight ? -overflow : innerWidth * .2;
                const x = from + (to - from) * current;
                track.style.transform = `translate3d(${x}px,0,0)`;
            });
            const introOpacity = Math.max(0, 1 - current * 4.6);
            if (heading) {
                heading.style.opacity = String(introOpacity);
                heading.style.transform = `translate3d(0,${current * -42}px,0)`;
                heading.style.pointerEvents = introOpacity > .25 ? '' : 'none';
            }
            if (stage) stage.style.setProperty('--gallery-intro-opacity', String(introOpacity));
            if (progressBar) progressBar.style.transform = `scaleX(${current})`;

            if (Math.abs(target - current) > .0005) frame = requestAnimationFrame(render);
            else frame = 0;
        };

        const requestRender = () => {
            target = measureProgress();
            if (!frame) frame = requestAnimationFrame(render);
        };
        const reset = () => {
            cancelAnimationFrame(frame);
            frame = 0;
            target = current = measureProgress();
            if (!wideScreen.matches || reducedMotion.matches) {
                tracks.forEach(track => { track.style.transform = ''; });
                if (heading) {
                    heading.style.opacity = '';
                    heading.style.transform = '';
                    heading.style.pointerEvents = '';
                }
                if (stage) stage.style.removeProperty('--gallery-intro-opacity');
                if (progressBar) progressBar.style.transform = '';
                return;
            }
            requestRender();
        };

        addEventListener('scroll', requestRender, { passive: true });
        addEventListener('resize', reset, { passive: true });
        wideScreen.addEventListener('change', reset);
        reducedMotion.addEventListener('change', reset);
        tracks.forEach(track => track.querySelectorAll('img').forEach(image => {
            if (!image.complete) image.addEventListener('load', requestRender, { once: true });
        }));
        reset();
    });

    // Route planning and permission states are handled in section-refinement.js.

    const galleryDialog = document.querySelector('#bj-gallery-dialog');
    if (galleryDialog && typeof galleryDialog.showModal === 'function') {
        const image = galleryDialog.querySelector('img');
        const caption = galleryDialog.querySelector('p');
        document.querySelectorAll('[data-gallery-open]').forEach(button => {
            button.addEventListener('click', () => {
                image.src = button.dataset.gallerySrc;
                image.alt = button.dataset.galleryCaption || 'Bangkiang Jaran';
                caption.textContent = button.dataset.galleryCaption || '';
                galleryDialog.showModal();
            });
        });
        galleryDialog.querySelector('[data-gallery-close]').addEventListener('click', () => galleryDialog.close());
        galleryDialog.addEventListener('click', event => {
            if (event.target === galleryDialog) galleryDialog.close();
        });
        galleryDialog.addEventListener('close', () => { image.src = ''; });
    }
    const parallaxImages = [...document.querySelectorAll('[data-bj-parallax] img')];
    if (motionFeatures.scrollParallax && parallaxImages.length && gsap && ScrollTrigger && !reducedMotion.matches && ['high', 'medium'].includes(motionLevel)) {
        parallaxImages.forEach(image => {
            gsap.fromTo(image,
                { yPercent: -2.5, scale: 1.07 },
                {
                    yPercent: 2.5,
                    scale: 1.07,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: image.parentElement,
                        start: 'top bottom',
                        end: 'bottom top',
                        scrub: .8,
                    },
                });
        });
    }

    // Two continuous photo ribbons. Scrolling adds a velocity impulse that eases
    // out gradually, so both tracks feel connected to the page without jolting.
    const ribbonElements = [...document.querySelectorAll('[data-bj-marquee]')];
    if (motionFeatures.ribbonMotion && ribbonElements.length) {
        const compactScreen = matchMedia('(max-width: 700px)');
        const ribbons = ribbonElements.map((element, index) => ({
            element,
            direction: element.dataset.direction === 'right' ? 1 : -1,
            baseSpeed: compactScreen.matches ? (index === 0 ? 18 : 16) : (index === 0 ? 28 : 24),
            distance: index === 0 ? 0 : 180,
            cycleWidth: 0,
        }));
        let previousTime = performance.now();
        let previousScroll = scrollY;
        let targetBoost = 0;
        let boost = 0;
        let animationFrame = 0;
        let ribbonInView = false;

        const measureRibbons = () => {
            ribbons.forEach(ribbon => {
                const firstSet = ribbon.element.querySelector('.bj-home-ribbon__set');
                const gap = parseFloat(getComputedStyle(ribbon.element).gap) || 0;
                ribbon.cycleWidth = firstSet ? firstSet.getBoundingClientRect().width + gap : 0;
                if (ribbon.cycleWidth) ribbon.distance %= ribbon.cycleWidth;
            });
        };

        const resetMotion = () => {
            ribbons.forEach(ribbon => { ribbon.element.style.transform = ''; });
            previousTime = performance.now();
            targetBoost = 0;
            boost = 0;
        };

        const animateRibbons = now => {
            animationFrame = 0;
            if (!ribbonInView || reducedMotion.matches || document.hidden) return;

            const deltaSeconds = Math.min((now - previousTime) / 1000, .05);
            previousTime = now;

            const response = 1 - Math.exp(-deltaSeconds * 7);
            boost += (targetBoost - boost) * response;
            targetBoost *= Math.exp(-deltaSeconds / 1.35);

            ribbons.forEach(ribbon => {
                if (!ribbon.cycleWidth) return;
                ribbon.distance = (ribbon.distance + (ribbon.baseSpeed + boost) * deltaSeconds) % ribbon.cycleWidth;
                const x = ribbon.direction < 0
                    ? -ribbon.distance
                    : ribbon.distance - ribbon.cycleWidth;
                ribbon.element.style.transform = `translate3d(${x}px,0,0)`;
            });

            animationFrame = requestAnimationFrame(animateRibbons);
        };

        const syncRibbonAnimation = () => {
            if (ribbonInView && !reducedMotion.matches && !document.hidden) {
                previousTime = performance.now();
                if (!animationFrame) animationFrame = requestAnimationFrame(animateRibbons);
                return;
            }
            cancelAnimationFrame(animationFrame);
            animationFrame = 0;
        };

        addEventListener('scroll', () => {
            const delta = Math.abs(scrollY - previousScroll);
            previousScroll = scrollY;
            targetBoost = Math.min(230, targetBoost + delta * 2.35);
        }, { passive: true });
        addEventListener('resize', measureRibbons, { passive: true });
        compactScreen.addEventListener('change', () => {
            ribbons.forEach((ribbon, index) => {
                ribbon.baseSpeed = compactScreen.matches ? (index === 0 ? 18 : 16) : (index === 0 ? 28 : 24);
            });
            measureRibbons();
        });
        reducedMotion.addEventListener('change', () => {
            resetMotion();
            syncRibbonAnimation();
        });
        document.addEventListener('visibilitychange', syncRibbonAnimation);
        ribbonElements.forEach(element => element.querySelectorAll('img').forEach(image => {
            if (!image.complete) image.addEventListener('load', measureRibbons, { once: true });
        }));
        measureRibbons();
        const ribbonRegion = ribbonElements[0].closest('.bj-home-ribbons, .bj-resto-stitch__ribbon, .bj-ticket-ribbons')
            || ribbonElements[0];
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(entries => {
                ribbonInView = entries.some(entry => entry.isIntersecting);
                syncRibbonAnimation();
            }, { rootMargin: '120px 0px' });
            observer.observe(ribbonRegion);
        } else {
            ribbonInView = true;
            syncRibbonAnimation();
        }
    }

    const ticketEstimator = document.querySelector('[data-ticket-estimator]');
    if (ticketEstimator) {
        const choices = [...ticketEstimator.querySelectorAll('[data-ticket-choice]')];
        const quantityInput = ticketEstimator.querySelector('[data-ticket-quantity]');
        const totalOutput = ticketEstimator.querySelector('[data-ticket-total]');
        const nameOutput = ticketEstimator.querySelector('[data-ticket-estimate-name]');
        const bookingLink = ticketEstimator.querySelector('[data-ticket-booking-link]');
        const currency = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        const updateTicketEstimate = () => {
            const selected = choices.find(choice => choice.checked) || choices[0];
            if (!selected) return;
            const quantity = Math.max(1, Number.parseInt(quantityInput?.value || '1', 10) || 1);
            const price = Number.parseInt(selected.dataset.price || '0', 10) || 0;
            if (quantityInput && quantityInput.value !== String(quantity)) quantityInput.value = String(quantity);
            if (totalOutput) totalOutput.textContent = currency.format(price * quantity).replace(/\s/g, ' ');
            if (nameOutput) nameOutput.textContent = selected.dataset.name || '';
            if (bookingLink && selected.dataset.bookingUrl) bookingLink.href = selected.dataset.bookingUrl;
        };

        choices.forEach(choice => choice.addEventListener('change', updateTicketEstimate));
        quantityInput?.addEventListener('input', updateTicketEstimate);
        updateTicketEstimate();
    }

    const items = [...document.querySelectorAll('.bj-reveal, .bj-motion-media, [data-bj-split], [data-aos], .fade-in, .fade-in-left, .fade-in-right')];
    const showAllMotionContent = () => {
        items.forEach(item => {
            item.classList.add('is-visible', 'show');
            if (gsap) gsap.set(item, { clearProps: 'opacity,visibility,transform,filter,clipPath' });
        });
    };

    if (!gsap || !ScrollTrigger || reducedMotion.matches) {
        showAllMotionContent();
        return;
    }

    const heroTitleWords = [...document.querySelectorAll('.bj-home-title .bj-word > span')];
    const heroTimeline = document.querySelector('.bj-home-hero__copy');
    if (heroTimeline && motionLevel === 'high' && document.body.classList.contains('bj-nature')) {
        gsap.from(heroTimeline, { y: 14, duration: .5, ease: 'power2.out', clearProps: 'transform' });
    }
    if (heroTimeline && motionLevel === 'high' && !document.body.classList.contains('bj-nature')) {
        const timeline = gsap.timeline({ defaults: { ease: 'power3.out' } });
        timeline
            .from('.bj-home-hero__copy .bj-home-kicker', { autoAlpha: 0, y: 16, duration: .65 })
            .from(heroTitleWords.length ? heroTitleWords : '.bj-home-title', {
                autoAlpha: 0,
                yPercent: heroTitleWords.length ? 112 : 0,
                y: heroTitleWords.length ? 0 : 32,
                duration: 1.05,
                stagger: .085,
            }, '-=.34')
            .from('.bj-home-hero__lead', { autoAlpha: 0, y: 20, duration: .7 }, '-=.52')
            .from('.bj-home-hero__actions > *', {
                autoAlpha: document.body.classList.contains('bj-marketing') ? 1 : 0,
                y: document.body.classList.contains('bj-marketing') ? 0 : 16,
                duration: .62,
                stagger: .1,
            }, '-=.4')
            .from('.bj-home-hero__bottom, .bj-home-hero__scroll', { autoAlpha: 0, duration: .55, stagger: .08 }, '-=.28');
    }

    if (document.querySelector('.bj-auth-card')) {
        gsap.from('.bj-auth-card', { autoAlpha: 0, y: 18, duration: .62, ease: 'power2.out' });
    }

    const heroElements = new Set(document.querySelectorAll('.bj-home-hero .bj-reveal, .bj-home-hero [data-bj-split]'));
    const splitElements = items.filter(item => item.matches('[data-bj-split]') && !heroElements.has(item));
    const animateSplitElement = (element, withScrollTrigger = false) => {
        const words = element.querySelectorAll('.bj-word > span');
        if (!words.length) return;
        gsap.from(words, {
            autoAlpha: 0,
            yPercent: 108,
            duration: motionLevel === 'low' ? .58 : .78,
            stagger: motionLevel === 'low' ? .045 : .075,
            ease: 'power3.out',
            ...(withScrollTrigger ? { scrollTrigger: { trigger: element, start: 'top 88%', once: true } } : {}),
        });
    };

    const standardItems = items.filter(item => {
        if (heroElements.has(item) || item.matches('[data-bj-split]')) return false;
        if (motionLevel === 'minimal' && !item.matches('[data-aos]')) return false;
        if (motionLevel === 'low' && page === 'wisatawan.pemesanan.create') {
            return Boolean(item.closest('.bj-booking-hero'))
                || item.matches('.bj-booking-section__head, .bj-booking-summary');
        }
        if (motionLevel === 'low' && page === 'wisatawan.pemesanan.riwayat' && item.matches('.bj-history-card')) return false;
        return true;
    });

    const animateStandardItem = (item, withScrollTrigger = false) => {
        const isMedia = item.classList.contains('bj-motion-media');
        const direction = item.dataset.aos === 'fade-left' ? 14 : item.dataset.aos === 'fade-right' ? -14 : 0;
        const order = Math.min(Number(item.style.getPropertyValue('--bj-reveal-order')) || 0, 4);
        gsap.from(item, {
            autoAlpha: 0,
            x: direction,
            y: direction ? 0 : motionLevel === 'high' ? 28 : motionLevel === 'medium' ? 20 : 12,
            scale: isMedia ? .985 : 1,
            clipPath: isMedia ? 'inset(0 0 12% 0 round 18px)' : 'inset(0 0 0 0)',
            duration: motionLevel === 'high' ? .78 : motionLevel === 'medium' ? .68 : .48,
            delay: ['high', 'medium'].includes(motionLevel) ? order * .06 : 0,
            ease: 'power3.out',
            clearProps: 'opacity,visibility,transform,filter,clipPath',
            ...(withScrollTrigger ? { scrollTrigger: { trigger: item, start: 'top 90%', once: true } } : {}),
        });
    };

    let revealObserver = null;
    if ('IntersectionObserver' in window) {
        revealObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                revealObserver.unobserve(entry.target);
                if (entry.target.matches('[data-bj-split]')) animateSplitElement(entry.target);
                else animateStandardItem(entry.target);
            });
        }, { rootMargin: '0px 0px -8%', threshold: .04 });
        splitElements.forEach(element => revealObserver.observe(element));
        standardItems.forEach(item => revealObserver.observe(item));
    } else {
        splitElements.forEach(element => animateSplitElement(element, true));
        standardItems.forEach(item => animateStandardItem(item, true));
    }

    if (location.hash) {
        const anchor = document.getElementById(location.hash.slice(1));
        anchor?.querySelectorAll('.bj-reveal, .bj-motion-media, [data-bj-split]').forEach(item => gsap.set(item, { clearProps: 'all' }));
    }
    reducedMotion.addEventListener('change', () => {
        if (reducedMotion.matches) {
            revealObserver?.disconnect();
            ScrollTrigger.getAll().forEach(trigger => trigger.kill());
            showAllMotionContent();
        }
    });
    addEventListener('load', () => ScrollTrigger.refresh(), { once: true });
});
