const EvoFrame = {
 init: () => {
  const newStyle = document.createElement('link');
  newStyle.setAttribute('rel', 'stylesheet');
  newStyle.setAttribute('href', 'assets/css/core/components.css');
  const style = document.querySelector('link[rel="stylesheet"][href="assets/css/core/styles.css"]');
  document.head.insertBefore(newStyle, style);
 }
}
export default EvoFrame;