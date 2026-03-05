(function () {
  const source = document.getElementById('md-source');
  const render = document.getElementById('md-render');

  if (source && render && window.marked) {
    marked.setOptions({ mangle: false, headerIds: false });
    render.innerHTML = marked.parse(source.value || '');
  }
})();
