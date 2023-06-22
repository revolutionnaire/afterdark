document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('hamburger').addEventListener('click', function() {
    document.getElementById('navigation').classList.toggle('show');
  });
});

window.addEventListener('scroll', function() {
  var viewport = window.innerWidth || document.documentElement.clientWidth;
  var iPadMini = 768; // Smallest supported tablet

  if (document.body.classList.contains('logged-in') === true && viewport < iPadMini) {
    var navigation = document.getElementById('navigation');
    var navigationHeight = navigation.offsetHeight;
    var wpadminbarHeight = document.getElementById('wpadminbar').offsetHeight;
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;

    if (scrollTop >= wpadminbarHeight) {
      navigation.classList.add('scrolling');
    } else {
      navigation.classList.remove('scrolling');
    }
  }
});
