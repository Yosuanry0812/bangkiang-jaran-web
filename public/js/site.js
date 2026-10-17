document.documentElement.classList.add('js-motion');

document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
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
        let index = 0;
        let timer;
        let paused = false;
        const duration = 6800;
        const schedule = () => {
            clearTimeout(timer);
            if (progress) {
                progress.style.transition = 'none';
                progress.style.transform = 'scaleX(0)';
            }
            if (slides.length < 2 || paused || reducedMotion.matches || document.hidden) return;
            requestAnimationFrame(() => requestAnimationFrame(() => {
                if (progress) {
                    progress.style.transition = `transform ${duration}ms linear`;
                    progress.style.transform = 'scaleX(1)';
                }
            }));
            timer = setTimeout(() => show(index + 1), duration);
        };
        const show = next => {
            index = (next + slides.length) % slides.length;
            slides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === index));
            if (current) current.textContent = String(index + 1).padStart(2, '0');
            if (caption) caption.textContent = slides[index].dataset.caption || 'Bangkiang Jaran';
            if (heroVideo) {
                if (index === 0 && !reducedMotion.matches) heroVideo.play().catch(() => {});
                else heroVideo.pause();
            }
            schedule();
        };
        hero.querySelector('[data-bj-hero-prev]')?.addEventListener('click', () => show(index - 1));
        hero.querySelector('[data-bj-hero-next]')?.addEventListener('click', () => show(index + 1));
        hero.addEventListener('pointerenter', () => { paused = true; clearTimeout(timer); });
        hero.addEventListener('pointerleave', () => { paused = false; schedule(); });
        hero.addEventListener('focusin', () => { paused = true; clearTimeout(timer); });
        hero.addEventListener('focusout', event => {
            if (hero.contains(event.relatedTarget)) return;
            paused = false;
            schedule();
        });
        document.addEventListener('visibilitychange', schedule);
        reducedMotion.addEventListener('change', schedule);
        let touchStart = 0;
        hero.addEventListener('touchstart', event => { touchStart = event.changedTouches[0].screenX; }, { passive: true });
        hero.addEventListener('touchend', event => {
            const distance = event.changedTouches[0].screenX - touchStart;
            if (Math.abs(distance) > 55) show(index + (distance < 0 ? 1 : -1));
        }, { passive: true });
        schedule();
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
        // Apply one motion language across public pages without requiring every
        // Blade template to repeat animation classes.
        motionRoot.querySelectorAll('h1, h2').forEach(heading => {
            if (heading.closest('[data-no-motion], dialog')) return;
            heading.dataset.bjSplit = '';
            heading.classList.add('bj-reveal');
        });

        const mediaContainers = new Set();
        motionRoot.querySelectorAll('img').forEach(image => {
            if (image.closest('[data-bj-hero-slide], [data-bj-gallery-track], .bj-home-ribbon, dialog')) return;
            const container = image.closest('figure, [class*="__media"], [class*="__photo"], .bj-gallery__item') || image;
            mediaContainers.add(container);
        });
        mediaContainers.forEach(container => container.classList.add('bj-motion-media'));

        motionRoot.querySelectorAll('article, [class*="-card"], [class*="__item"]').forEach(card => {
            if (card.closest('[data-bj-hero-slide], [data-bj-gallery-track], .bj-home-ribbon, dialog')) return;
            card.classList.add('bj-reveal');
        });

        motionRoot.querySelectorAll('section > p, section > div > p').forEach(copy => {
            if (copy.closest('.bj-reveal, [data-no-motion]')) return;
            copy.classList.add('bj-reveal');
        });

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
    if (parallaxImages.length && !reducedMotion.matches) {
        let pending = false;
        const updateParallax = () => {
            const screenCenter = innerHeight / 2;
            parallaxImages.forEach(image => {
                const bounds = image.parentElement.getBoundingClientRect();
                if (bounds.bottom < 0 || bounds.top > innerHeight) return;
                const shift = Math.max(-14, Math.min(14, (screenCenter - bounds.top - bounds.height / 2) * .035));
                image.style.transform = `translate3d(0,${shift}px,0) scale(1.07)`;
            });
            pending = false;
        };
        addEventListener('scroll', () => { if (!pending) { pending = true; requestAnimationFrame(updateParallax); } }, { passive: true });
        updateParallax();
    }

    // Two continuous photo ribbons. Scrolling adds a velocity impulse that eases
    // out gradually, so both tracks feel connected to the page without jolting.
    const ribbonElements = [...document.querySelectorAll('[data-bj-marquee]')];
    if (ribbonElements.length) {
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
            const deltaSeconds = Math.min((now - previousTime) / 1000, .05);
            previousTime = now;

            if (!reducedMotion.matches && !document.hidden) {
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
            }

            requestAnimationFrame(animateRibbons);
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
        reducedMotion.addEventListener('change', resetMotion);
        ribbonElements.forEach(element => element.querySelectorAll('img').forEach(image => {
            if (!image.complete) image.addEventListener('load', measureRibbons, { once: true });
        }));
        measureRibbons();
        requestAnimationFrame(animateRibbons);
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
            const quantity = Math.max(1, Math.min(30, Number.parseInt(quantityInput?.value || '1', 10) || 1));
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

    const items = document.querySelectorAll('.bj-reveal, .bj-motion-media, [data-bj-split]');
    if (!items.length) return;
    if (!('IntersectionObserver' in window) || reducedMotion.matches) {
        items.forEach(item => item.classList.add('is-visible'));
        return;
    }
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: .08, rootMargin: '0px 0px -28px 0px' });
    items.forEach(item => observer.observe(item));
    if (location.hash) {
        const anchor = document.getElementById(location.hash.slice(1));
        anchor?.querySelectorAll('.bj-reveal, .bj-motion-media, [data-bj-split]').forEach(item => item.classList.add('is-visible'));
    }
    reducedMotion.addEventListener('change', () => {
        if (reducedMotion.matches) items.forEach(item => item.classList.add('is-visible'));
    });
});
