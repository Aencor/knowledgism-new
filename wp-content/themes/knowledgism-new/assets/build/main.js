/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./wp-content/themes/knowledgism-new/assets/js/main.js":
/*!*************************************************************!*\
  !*** ./wp-content/themes/knowledgism-new/assets/js/main.js ***!
  \*************************************************************/
/***/ (() => {

/**
 * Knowledgism Theme Scripts
 * Matches prototype interactions, animations, and form handlers.
 */
document.addEventListener('DOMContentLoaded', function () {
  function q(s, r) {
    return (r || document).querySelector(s);
  }
  function qa(s, r) {
    return Array.from((r || document).querySelectorAll(s));
  }

  // 1. Mobile Menu Toggle
  var burger = document.getElementById('burgerToggle') || q('.burger');
  var nav = document.getElementById('siteNavigation') || q('nav.main');
  if (burger && nav) {
    burger.addEventListener('click', function (e) {
      e.preventDefault();
      var isOpen = nav.classList.toggle('open');
      burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    // Close mobile nav when clicking outside
    document.addEventListener('click', function (e) {
      if (!nav.contains(e.target) && !burger.contains(e.target) && nav.classList.contains('open')) {
        nav.classList.remove('open');
        burger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // 1.1 Language Switcher Dropdown
  var langBtn = document.getElementById('langSwitcherBtn');
  var langMenu = document.getElementById('langDropdown');
  if (langBtn && langMenu) {
    langBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var expanded = langBtn.getAttribute('aria-expanded') === 'true';
      langBtn.setAttribute('aria-expanded', !expanded);
      langMenu.classList.toggle('is-open');
    });
    document.addEventListener('click', function (e) {
      if (!langBtn.contains(e.target) && !langMenu.contains(e.target) && langMenu.classList.contains('is-open')) {
        langBtn.setAttribute('aria-expanded', 'false');
        langMenu.classList.remove('is-open');
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && langMenu.classList.contains('is-open')) {
        langBtn.setAttribute('aria-expanded', 'false');
        langMenu.classList.remove('is-open');
        langBtn.focus();
      }
    });
  }

  // 2. Prototype Scroll & Element Reveal Animations
  function baseAnimate() {
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    qa('.g2>.card, .g3>.card, .g4>.card, .rowbox, .newsband, .ladder .st').forEach(function (el) {
      if (!el.classList.contains('rv')) el.classList.add('rv');
    });
    var items = qa('.rv');
    if (reduce || !('IntersectionObserver' in window)) {
      items.forEach(function (el) {
        el.classList.add('in');
      });
    } else {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            e.target.classList.add('in');
            io.unobserve(e.target);
          }
        });
      }, {
        threshold: 0.12,
        rootMargin: '0px 0px -40px 0px'
      });
      items.forEach(function (el, i) {
        el.style.transitionDelay = Math.min(i % 4, 3) * 70 + 'ms';
        io.observe(el);
      });
      var zo = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (!e.isIntersecting) return;
          qa('i', e.target).forEach(function (seg, i) {
            seg.classList.add('zseg');
            seg.style.animationDelay = i * 90 + 'ms';
          });
          zo.unobserve(e.target);
        });
      }, {
        threshold: 0.3
      });
      qa('.zones').forEach(function (z) {
        zo.observe(z);
      });
    }
  }
  baseAnimate();

  // 3. Cookie Bar
  function baseCookies() {
    var bar = q('#cookieBar');
    if (!bar) return;
    var choice = localStorage.getItem('knowledgism_cookies_accepted');
    if (!choice) {
      bar.hidden = false;
    }
    var acceptBtn = q('#ckAccept');
    if (acceptBtn) {
      acceptBtn.addEventListener('click', function () {
        bar.hidden = true;
        localStorage.setItem('knowledgism_cookies_accepted', 'all');
      });
    }
    var rejectBtn = q('#ckReject');
    if (rejectBtn) {
      rejectBtn.addEventListener('click', function () {
        bar.hidden = true;
        localStorage.setItem('knowledgism_cookies_accepted', 'essential');
      });
    }
    var reopenBtn = q('#reopenCookies');
    if (reopenBtn) {
      reopenBtn.addEventListener('click', function () {
        bar.hidden = false;
        window.scrollTo({
          top: document.body.scrollHeight,
          behavior: 'smooth'
        });
      });
    }
  }
  baseCookies();

  // 4. Newsletter Form Handler
  function baseNewsletter() {
    qa('.newsband').forEach(function (band) {
      var go = q('.nGo', band);
      var mail = q('.nMail', band);
      var ok = q('.nOk', band);
      var txt = q('.newsTxt', band);
      var form = q('.newsForm', band);
      var done = q('.newsDone', band);
      if (go && mail) {
        go.addEventListener('click', function () {
          var m = mail.value.trim();
          if (m.indexOf('@') < 1 || m.indexOf('.') < 0) {
            mail.focus();
            alert('Enter a valid email address.');
            return;
          }
          if (ok && !ok.checked) {
            alert('Please accept the Privacy Policy to subscribe.');
            return;
          }
          if (txt) {
            txt.textContent = 'Confirmed. It goes to ' + m + ' every Monday.';
          }
          if (form) form.hidden = true;
          if (done) done.hidden = false;
        });
        mail.addEventListener('keydown', function (e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            go.click();
          }
        });
      }
    });
  }
  baseNewsletter();

  // 5. Foundations Sidebar Navigation via AJAX
  function baseFoundationsAjax() {
    var sidebar = q('.foundations-sidebar');
    var contentArea = q('#foundations-content-area');
    var heroTitle = q('#foundations-hero-title');
    if (!sidebar || !contentArea) return;
    var links = qa('a', sidebar);
    function loadFoundationsPage(url, push) {
      if (push === undefined) push = true;
      contentArea.style.opacity = '0.5';
      contentArea.style.transition = 'opacity 0.2s ease';
      var fetchUrl = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'ajax_foundations=1';
      fetch(fetchUrl).then(function (res) {
        return res.json();
      }).then(function (data) {
        if (data && data.success && data.data) {
          var d = data.data;
          var html = d.content || '';
          if (d.paradigms_html) {
            html += d.paradigms_html;
          }
          contentArea.innerHTML = html;
          if (heroTitle && d.hero_title) {
            heroTitle.textContent = d.hero_title;
          }
          links.forEach(function (link) {
            var linkSlug = link.getAttribute('data-slug');
            var linkId = link.getAttribute('data-id');
            if (linkSlug === d.slug || linkId && parseInt(linkId, 10) === d.id) {
              link.classList.add('on');
            } else {
              link.classList.remove('on');
            }
          });
          if (d.title) {
            document.title = d.title + ' — Knowledgism';
          }
          if (push && window.history && window.history.pushState) {
            window.history.pushState({
              foundations: true,
              url: url
            }, d.title, url);
          }
          var mainEl = q('#foundations-main');
          if (mainEl && window.innerWidth < 900) {
            mainEl.scrollIntoView({
              behavior: 'smooth'
            });
          }
        } else {
          window.location.href = url;
        }
      })["catch"](function (err) {
        console.error('Foundations AJAX error:', err);
        window.location.href = url;
      })["finally"](function () {
        contentArea.style.opacity = '1';
      });
    }
    links.forEach(function (link) {
      link.addEventListener('click', function (e) {
        var href = link.getAttribute('href');
        if (!href || href.startsWith('#')) return;
        e.preventDefault();
        if (link.classList.contains('on')) return;
        loadFoundationsPage(href, true);
      });
    });
    window.addEventListener('popstate', function (e) {
      if (e.state && e.state.foundations && e.state.url) {
        loadFoundationsPage(e.state.url, false);
      }
    });
  }
  baseFoundationsAjax();

  // 6. Training Glossary Interactive Engine (Live Search & A-Z Filtering)
  function baseGlossary() {
    var az = q('#azbar');
    var list = q('#glosList');
    var search = q('#glosSearch');
    var countEl = q('#glosCount');
    if (!az || !list) return;
    var dataEl = document.getElementById('glossary-dataset');
    var terms = [];
    if (dataEl && dataEl.textContent) {
      try {
        terms = JSON.parse(dataEl.textContent);
      } catch (err) {
        console.error('Error parsing glossary dataset:', err);
      }
    }
    if (!terms || !terms.length) return;
    var have = {};
    terms.forEach(function (g) {
      var first = g.word && g.word[0] ? g.word[0].toUpperCase() : '';
      if (first) have[first] = (have[first] || 0) + 1;
    });
    var urlParams = new URLSearchParams(window.location.search);
    var initialLetter = (urlParams.get('letter') || 'A').toUpperCase();
    var letter = have[initialLetter] ? initialLetter : Object.keys(have).sort()[0] || 'A';
    var searchTerm = '';
    if (window.location.hash && window.location.hash.startsWith('#term-')) {
      var targetSlug = window.location.hash.replace('#term-', '');
      var match = terms.find(function (t) {
        return t.slug === targetSlug;
      });
      if (match && match.letter) {
        letter = match.letter;
      }
    }
    function renderGlossary() {
      var alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
      az.innerHTML = alphabet.map(function (L) {
        var isAvailable = !!have[L];
        var isCurrent = L === letter && !searchTerm;
        var disabledAttr = isAvailable ? '' : ' disabled';
        var classStr = 'az' + (isCurrent ? ' on' : '');
        return '<button type="button" class="' + classStr + '" data-l="' + L + '"' + disabledAttr + ' title="' + (isAvailable ? have[L] + ' terms' : 'No terms') + '">' + L + '</button>';
      }).join('');
      qa('.az', az).forEach(function (b) {
        b.addEventListener('click', function () {
          if (b.disabled) return;
          letter = b.getAttribute('data-l');
          searchTerm = '';
          if (search) search.value = '';
          renderGlossary();
        });
      });
      var filtered = terms.filter(function (t) {
        if (searchTerm) {
          var w = (t.word || '').toLowerCase();
          var m = (t.meaning || '').toLowerCase();
          return w.indexOf(searchTerm) > -1 || m.indexOf(searchTerm) > -1;
        }
        return t.letter === letter;
      });
      if (countEl) {
        if (searchTerm) {
          countEl.textContent = filtered.length + ' match' + (filtered.length === 1 ? '' : 'es') + ' for "' + searchTerm + '"';
        } else {
          countEl.textContent = 'Showing ' + filtered.length + ' term' + (filtered.length === 1 ? '' : 's') + ' under ' + letter;
        }
      }
      if (!filtered.length) {
        list.innerHTML = '<p class="sm" style="grid-column:1/-1;padding:24px 0;color:var(--muted)">No matches found.</p>';
        return;
      }
      list.innerHTML = filtered.map(function (t) {
        var zoneBadge = '';
        if (t.zone) {
          var zColor = t.zone === 'gold' ? 'var(--gold)' : t.zone === 'red' ? 'var(--red)' : 'var(--green)';
          zoneBadge = '<span class="tiny" style="color:' + zColor + ';margin-left:8px;font-weight:600">' + t.zone.toUpperCase() + ' ZONE</span>';
        }
        return '<div id="term-' + t.slug + '" style="padding:14px 0;border-top:1px solid var(--line)">' + '<div style="font-weight:700;font-family:var(--mono);font-size:13px;letter-spacing:.06em">' + t.word + zoneBadge + '</div>' + '<p class="sm mt8" style="color:#4A5764;line-height:1.6">' + t.meaning + '</p>' + '</div>';
      }).join('');
      if (window.location.hash && window.location.hash.startsWith('#term-')) {
        var el = document.querySelector(window.location.hash);
        if (el) {
          setTimeout(function () {
            el.scrollIntoView({
              behavior: 'smooth',
              block: 'center'
            });
            el.style.backgroundColor = 'rgba(89, 186, 204, 0.1)';
            el.style.transition = 'background-color 0.5s ease';
            setTimeout(function () {
              el.style.backgroundColor = 'transparent';
            }, 2000);
          }, 100);
        }
      }
    }
    if (search) {
      search.addEventListener('input', function () {
        searchTerm = this.value.toLowerCase().trim();
        renderGlossary();
      });
    }
    renderGlossary();
  }
  baseGlossary();

  // 10. Home Carousel
  function baseHomeCarousel() {
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
        if (track) {
          track.style.transform = 'translateX(-' + currentIndex * 100 + '%)';
        }
        slides.forEach(function (slide, i) {
          var isActive = i === currentIndex;
          slide.classList.toggle('active', isActive);
          slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        });
        tabs.forEach(function (tab, i) {
          var isActive = i === currentIndex;
          tab.classList.toggle('active', isActive);
          tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
        dots.forEach(function (dot, i) {
          dot.classList.toggle('active', i === currentIndex);
        });
      }
      function nextSlide() {
        goToSlide(currentIndex + 1);
      }
      function prevSlide() {
        goToSlide(currentIndex - 1);
      }
      function startAutoplay() {
        if (autoplayDelay <= 0 || isPaused) return;
        stopAutoplay();
        autoplayTimer = setInterval(function () {
          if (!isPaused) nextSlide();
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
      wrapper.addEventListener('mouseenter', function () {
        isPaused = true;
        stopAutoplay();
      });
      wrapper.addEventListener('mouseleave', function () {
        isPaused = false;
        startAutoplay();
      });
      wrapper.addEventListener('focusin', function () {
        isPaused = true;
        stopAutoplay();
      });
      wrapper.addEventListener('focusout', function () {
        isPaused = false;
        startAutoplay();
      });
      var startX = 0,
        startY = 0,
        deltaX = 0,
        deltaY = 0,
        isSwiping = false;
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
      }, {
        passive: true
      });
      wrapper.addEventListener('touchmove', function (e) {
        if (!isSwiping || e.touches.length !== 1) return;
        deltaX = e.touches[0].clientX - startX;
        deltaY = e.touches[0].clientY - startY;
      }, {
        passive: true
      });
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
      }, {
        passive: true
      });
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
  baseHomeCarousel();
});

/***/ }),

/***/ "./wp-content/themes/knowledgism-new/assets/scss/style.scss":
/*!******************************************************************!*\
  !*** ./wp-content/themes/knowledgism-new/assets/scss/style.scss ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/main": 0,
/******/ 			"style": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkCodebymonk"] = self["webpackChunkCodebymonk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["style"], () => (__webpack_require__("./wp-content/themes/knowledgism-new/assets/js/main.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["style"], () => (__webpack_require__("./wp-content/themes/knowledgism-new/assets/scss/style.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=main.js.map