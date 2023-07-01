import LocomotiveScroll from 'locomotive-scroll';

document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementsByClassName('page-about').length != 0) {
    const scroll = new LocomotiveScroll({
      el: document.querySelector('[data-scroll-container]'),
      smooth: true,
      smartphone: {
        smooth: true
      },
      table: {
        smooth: true
      }
    });

    document.addEventListener('DOMContentLoaded', function() {
      scroll.update();
    });
  }
});
