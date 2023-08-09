import Lenis from '@studio-freight/lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

document.addEventListener('DOMContentLoaded', function() {
  // Setup Lenis
  const lenis = new Lenis({
    smoothWheel: true,
    smoothTouch: true
  });

  lenis.on('scroll', ScrollTrigger.update);

  gsap.ticker.add((time)=>{
    lenis.raf(time * 1000)
  });

  gsap.ticker.lagSmoothing(0);

  // Setup scrollTo link
  document.querySelector('[href="#what-will-we-write"]').addEventListener('click', function(event) {
    event.preventDefault();
    lenis.scrollTo('#what-will-we-write', {
      offset: -96
    });
  });

  // Setup GSAP animations
  gsap.registerPlugin(ScrollTrigger);

  // Animate header intro
  let firstSectionIn = gsap.timeline();

  firstSectionIn.from('#headline-adventures', {
    yPercent: 50,
    opacity: 0
  });

  firstSectionIn.from('#content-discover', {
    yPercent: 50,
    opacity: 0
  },
  '+=0.05');

  firstSectionIn.from('#link-lets-go', {
    yPercent: 50,
    opacity: 0
  },
  '+=0.05');

  // Animate second section intro
  let secondSectionIn = gsap.timeline({
    scrollTrigger: {
      trigger: '#what-will-we-write',
      start: 'top bottom',
      end: 'center center',
      scrub: true
    }
  });

  secondSectionIn.from('#content-what-will-we-write', {
    yPercent: -300,
    opacity: 0,
  });

  // Animate third section intro
  let thirdSectionIn = gsap.timeline({
    scrollTrigger: {
      trigger: '#fun-activities',
      start: 'top bottom',
      end: 'center center',
      scrub: true
    }
  });

  thirdSectionIn.from('#content-nighttime-tours', {
    yPercent: -300,
    opacity: 0,
  });

  thirdSectionIn.from('#star', {
    yPercent: -100,
    opacity: 0
  });

  thirdSectionIn.from('#stars-left', {
    yPercent: -160,
    opacity: 0,
    delay: -0.25
  });

  thirdSectionIn.from('#stars-right', {
    yPercent: -180,
    opacity: 0,
    delay: -0.25
  });

  // Animate fourth section intro
  let fourthSectionIn = gsap.timeline({
    scrollTrigger: {
      trigger: '#local-music-venues',
      start: 'top bottom',
      end: 'center center',
      scrub: true
    }
  });

  fourthSectionIn.from('#cocktail-glass', {
    yPercent: -180,
    opacity: 0
  });

  fourthSectionIn.from('#cocktail-olive', {
    yPercent: -160,
    opacity: 0,
    delay: -0.25
  });

  fourthSectionIn.from('#content-local-venues', {
    yPercent: 150,
    opacity: 0
  });

  // Animate fifth section intro
  let fifthSectionIn = gsap.timeline({
    scrollTrigger: {
      trigger: '#coffee',
      start: 'top bottom',
      end: 'center center',
      scrub: true
    }
  });

  fifthSectionIn.from('#coffee', {
    xPercent: 200,
    opacity: 0
  });

  fifthSectionIn.from('#content-24-7', {
    yPercent: 100,
    opacity: 0
  });

  // Animate sixth section intro
  let sixthSectionIn = gsap.timeline({
    scrollTrigger: {
      trigger: '#neighborhoods',
      start: 'top bottom',
      end: 'center center',
      scrub: true
    }
  })

  sixthSectionIn.from('#buildings-left', {
    y: -100,
    opacity: 0
  });

  sixthSectionIn.from('#buildings-center', {
    y: -240,
    opacity: 0,
    delay: -0.25
  });

  sixthSectionIn.from('#buildings-right', {
    y: -180,
    opacity: 0,
    delay: -0.25
  });

  sixthSectionIn.from('#content-neighborhoods', {
    yPercent: 200,
    opacity: 0
  });
});
