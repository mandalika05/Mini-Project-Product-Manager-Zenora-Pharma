// Tema gelap/terang (disimpan di localStorage) + flash alert
(function () {
  try { var t = localStorage.getItem("zp-theme"); if (t) document.documentElement.setAttribute("data-theme", t); } catch (e) {}
})();
document.addEventListener("DOMContentLoaded", function () {
  var btn = document.getElementById("themeToggle");
  if (btn) btn.addEventListener("click", function () {
    var r = document.documentElement;
    var n = r.getAttribute("data-theme") === "dark" ? "light" : "dark";
    r.setAttribute("data-theme", n);
    try { localStorage.setItem("zp-theme", n); } catch (e) {}
  });
  var a = document.querySelector(".alert");
  if (a) {
    var x = a.querySelector(".close");
    if (x) x.addEventListener("click", function () { a.classList.add("hide"); });
    setTimeout(function () { a.classList.add("hide"); }, 4500);
  }
});
