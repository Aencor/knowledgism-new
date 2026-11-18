/**
 * Knowledgism Home Carousel Block JavaScript (Regla 3: GSAP + Modular)
 * Handles slide transitions, tab switching, arrow controls, touch swipe, and autoplay.
 */
(function () {
  'use strict';

  function initHomeCarousels() {
    var carousels = document.querySelectorAll('.home-carousel-wrapper');
    if (!carousels.length) return;

    carousels.forEach(function (wrapper) {
      if (wrapper.dataset.carouselInitialized === 'true') return;
      wrapper.dataset.carouselInitialized = 'true';

      var track = wrapper.querySelector('.home-carousel-track');
      var slides = Array.from(wrapper.querySelectorAll('.home-carousel-slide'));
      var tabs = Array.from(wrapper.querySelectorAll('.home-carousel-tab'));
      var dots = Array.from(wrapper.querySelectorAll('.home-carousel-dot'));
      var prevBtn = wrapper.querySelector('.home-carousel-nav-btn.prev');
      var nextBtn = wrapper.querySelector('.home-carousel-nav-btn.next');

      var total = slides.length;
      if (total <= 1) return;

      var currentIndex = 0;
      var autoplayTimer = null;
      var autoplayDelay = parseInt(wrapper.dataset.autoplay, 10) || 6000;
      var isPaused = false;

      function goToSlide(index) {
        if (index < 0) {
          currentIndex = total - 1;
        } else if (index >= total) {
          currentIndex = 0;
        } else {
          currentIndex = index;
        }

        // Animate with GSAP if available, otherwise CSS transform
        if (window.gsap && track) {
          window.gsap.to(track, {
            xPercent: -currentIndex * 100,
            duration: 0.5,
            ease: 'power2.out'
          });

          var activeSlide = slides[currentIndex];
          if (activeSlide) {
            var card = activeSlide.querySelector('.home-slide-card');
            if (card) {
              window.gsap.fromTo(card, 
                { opacity: 0.9, y: 6 },
                { opacity: 1, y: 0, duration: 0.4, ease: 'power2.out' }
              );
            }
          }
        } else if (track) {
          track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
        }

        // Update active slide state
        slides.forEach(function (slide, i) {
          var isActive = (i === currentIndex);
          slide.classList.toggle('active', isActive);
          slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        });

        // Update tabs
        tabs.forEach(function (tab, i) {
          var isActive = (i === currentIndex);
          tab.classList.toggle('active', isActive);
          tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        // Update dots
        dots.forEach(function (dot, i) {
          dot.classList.toggle('active', i === currentIndex);
        });
      }

      function nextSlide() { goToSlide(currentIndex + 1); }
      function prevSlide() { goToSlide(currentIndex - 1); }

      function startAutoplay() {
        if (autoplayDelay <= 0 || isPaused) return;
        stopAutoplay();
        autoplayTimer = setInterval(function () {
          if (!isPaused) {
            nextSlide();
          }
        }, autoplayDelay);
      }

      function stopAutoplay() {
        if (autoplayTimer) {
          clearInterval(autoplayTimer);
          autoplayTimer = null;
        }
      }

      if (prevBtn) {
        prevBtn.addEventListener('click', function (e) {
          e.preventDefault();
          prevSlide();
          startAutoplay();
        });
      }

      if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
          e.preventDefault();
          nextSlide();
          startAutoplay();
        });
      }

      tabs.forEach(function (tab, i) {
        tab.addEventListener('click', function (e) {
          e.preventDefault();
          goToSlide(i);
          startAutoplay();
        });
      });

      dots.forEach(function (dot, i) {
        dot.addEventListener('click', function (e) {
          e.preventDefault();
          goToSlide(i);
          startAutoplay();
        });
      });

      wrapper.addEventListener('mouseenter', function () { isPaused = true; stopAutoplay(); });
      wrapper.addEventListener('mouseleave', function () { isPaused = false; startAutoplay(); });
      wrapper.addEventListener('focusin', function () { isPaused = true; stopAutoplay(); });
      wrapper.addEventListener('focusout', function () { isPaused = false; startAutoplay(); });

      // Touch / Swipe
      var startX = 0, startY = 0, deltaX = 0, deltaY = 0, isSwiping = false;

      wrapper.addEventListener('touchstart', function (e) {
        if (e.touches.length === 1) {
          startX = e.touches[0].clientX;
          startY = e.touches[0].clientY;
          deltaX = 0;
          deltaY = 0;
          isSwiping = true;
          isPaused = true;
          stopAutoplay();
        }
      }, { passive: true });

      wrapper.addEventListener('touchmove', function (e) {
        if (!isSwiping || e.touches.length !== 1) return;
        deltaX = e.touches[0].clientX - startX;
        deltaY = e.touches[0].clientY - startY;
      }, { passive: true });

      wrapper.addEventListener('touchend', function () {
        if (!isSwiping) return;
        isSwiping = false;
        isPaused = false;
        if (Math.abs(deltaX) > 45 && Math.abs(deltaX) > Math.abs(deltaY) * 1.3) {
          if (deltaX < 0) {
            nextSlide();
          } else {
            prevSlide();
          }
        }
        startAutoplay();
      }, { passive: true });

      wrapper.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') {
          prevSlide();
          startAutoplay();
        } else if (e.key === 'ArrowRight') {
          nextSlide();
          startAutoplay();
        }
      });

      goToSlide(0);
      startAutoplay();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHomeCarousels);
  } else {
    initHomeCarousels();
  }
})();
