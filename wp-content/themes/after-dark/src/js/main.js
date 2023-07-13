import { NavigationBar } from './navigation.js';
import Lenis from '@studio-freight/lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

document.addEventListener('DOMContentLoaded', function(){
  const navigationBar = new NavigationBar('#navigation');

  if (document.body.classList.contains('page-about')) {
    // Setup Lenis
    const lenis = new Lenis({
      smoothWheel: true,
      smoothTouch: true
    });

    lenis.on('scroll', (e) => {});

    const scrollFunction = (time) => {
      lenis.raf(time);
      requestAnimationFrame(scrollFunction);
    }

    requestAnimationFrame(scrollFunction);

    document.querySelector('[href="#fold-what-will-we-write"]').addEventListener('click', function(event) {
      event.preventDefault();
      lenis.scrollTo('#fold-what-will-we-write', {
        offset: -96
      });
    });

    // Setup GSAP animations
    gsap.registerPlugin(ScrollTrigger);

    let timeline = gsap.timeline();

    timeline.from('#headline-adventures', {
      yPercent: 50,
      opacity: 0
    });

    timeline.from('#content-budget', {
      yPercent: 50,
      opacity: 0
    },
    '+=0.05');

    timeline.from('#content-discover', {
      yPercent: 50,
      opacity: 0
    },
    '+=0.05');

    timeline.from('#link-lets-go', {
      yPercent: 50,
      opacity: 0
    },
    '+=0.05');

    gsap.from('#content-what-will-we-write', {
      scrollTrigger: {
        trigger: '#content-what-will-we-write',
        start: 'top center',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: -300,
      opacity: 0,
      delay: 0.1
    });

    gsap.from('#content-nighttime-tours', {
      scrollTrigger: {
        trigger: '#content-nighttime-tours',
        start: 'top center',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: -300,
      opacity: 0,
    });

    gsap.from('#star', {
      scrollTrigger: {
        trigger: '#content-nighttime-tours',
        start: 'top center',
        end: 'bottom top',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: -100,
      opacity: 0
    });

    gsap.from('#stars-left', {
      scrollTrigger: {
        trigger: '#content-nighttime-tours',
        start: 'top bottom',
        end: 'bottom top',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: -160,
      opacity: 0
    });

    gsap.from('#stars-right', {
      scrollTrigger: {
        trigger: '#content-nighttime-tours',
        start: 'top bottom',
        end: 'bottom top',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: -180,
      opacity: 0
    });

    gsap.from('#cocktail-glass', {
      scrollTrigger: {
        trigger: '#cocktail',
        start: 'top center',
        end: 'top top',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: -180,
      opacity: 0
    });

    gsap.from('#cocktail-olive', {
      scrollTrigger: {
        trigger: '#cocktail',
        start: 'center center',
        end: 'top top',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: -160,
      opacity: 0,
      delay: 0.1
    });

    gsap.from('#content-local-venues', {
      scrollTrigger: {
        trigger: '#cocktail',
        start: 'top center',
        end: 'bottom 33%',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: 150,
      opacity: 0
    });

    gsap.from('#coffee', {
      scrollTrigger: {
        trigger: '#coffee',
        start: 'top bottom',
        end: 'center center',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      xPercent: 200,
      opacity: 0
    });

    gsap.from('#content-24-7', {
      scrollTrigger: {
        trigger: '#coffee',
        start: 'top center',
        end: 'bottom center',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: 100,
      opacity: 0
    });

    gsap.from('#buildings-left', {
      scrollTrigger: {
        trigger: '#fold-neighborhoods',
        start: 'top center',
        end: 'center center',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      y: -100,
      opacity: 0
    });

    gsap.from('#buildings-center', {
      scrollTrigger: {
        trigger: '#fold-neighborhoods',
        start: 'top center',
        end: 'center center',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      y: -240,
      opacity: 0
    });

    gsap.from('#buildings-right', {
      scrollTrigger: {
        trigger: '#fold-neighborhoods',
        start: 'top center',
        end: 'center center',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      y: -180,
      opacity: 0
    });

    gsap.from('#content-neighborhoods', {
      scrollTrigger: {
        trigger: '#fold-neighborhoods',
        start: 'top center',
        end: 'center center',
        scrub: true,
        toggleActions: 'restart pause reverse reset'
      },
      yPercent: 200,
      opacity: 0
    });
  }
});
