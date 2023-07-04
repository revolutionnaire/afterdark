import Lenis from '@studio-freight/lenis';

document.addEventListener('DOMContentLoaded', function() {
  if (document.body.classList.contains('page-about')) {
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
  }
});
