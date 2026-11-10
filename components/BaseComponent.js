const init = () => {
 const newStyle = document.createElement('link');
 newStyle.setAttribute('rel', 'stylesheet');
 newStyle.setAttribute('href', 'assets/css/core/components.css');
 const style = document.querySelector('link[rel="stylesheet"][href="assets/css/core/styles.css"]');
 document.head.insertBefore(newStyle, style);
}
export class BaseComponent {
 #theId = null;
 #theElem = null;
 
 constructor() {
  const componentStyle = document.querySelector('link[rel="stylesheet"][href="assets/css/core/components.css"]');
  if (componentStyle == null) init();
 }
 setup(id, tpl, tplId, inputElement, mode = 'non-visual', elementType = 'input') {
  this.#theId = id; 
  let created = null;
  let elem = document.getElementById(id);
  if (elem == null) elem = document.querySelector(`[data-field="${id}"]`);
  if (mode == 'non-visual') {
   created = elem;
   if (elem == null) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(tpl, 'text/html');
    const html = doc.querySelector(`#${tplId}`);
    html.setAttribute('id', id);
    document.querySelector('body').appendChild(html);
    created = html;
   }
  }else {
   if (elem == null) elem = document.querySelector(id);
   if (elem == null) return false;
   const parser = new DOMParser();
   const doc = parser.parseFromString(tpl, 'text/html');
   const html = doc.querySelector(`#${tplId}`);
   html.setAttribute('id', id);
   if (inputElement !== null) {
    const interfaceElement = html.querySelector(inputElement);
    if (elementType == 'input') interfaceElement.setAttribute('data-field', elem.getAttribute('data-field'));
   }
//    const classAttr = elem.getAttribute('class');
   const styleAttr = elem.getAttribute('style');
//    if (classAttr !== undefined) html.setAttribute('class', classAttr);
   if (styleAttr !== undefined) html.setAttribute('style', styleAttr);
   elem.replaceWith(html);
   created = html;
  }
  this.#theElem = elem;
  return created;
 }
 createNewElement(tpl, id) {
  const parser = new DOMParser();
  const doc = parser.parseFromString(tpl, 'text/html');
  return doc.querySelector(id);
 }
 getAttr(elem, attr) {
  return elem.getAttribute(attr);
 }
 getInitialElement() {
  return this.#theElem;
 }
 getComponentId() {
  return this.#theId;
 }
}
export class Events {
 constructor(host) {
  this.proxy = document.createDocumentFragment();
  this.proxy.host = host;

  ["addEventListener", "dispatchEvent", "removeEventListener"].forEach(
   this.delegate,
   this
  );

  host.on = (eventName, func) => {
   host.addEventListener(eventName, func);
   return host;
  };
  host.off = (eventName, func) => {
   host.removeEventListener(eventName, func);
   return host;
  };
  host.emit = (eventName, options) => {
   host.dispatchEvent(new CustomEvent(eventName, options));
  }
 }
 delegate(method) {
  this.proxy.host[method] = this.proxy[method].bind(this.proxy);
 }
}
export const lightenDarkenColor = (color, percent) => {
 let num = parseInt(color.replace("#", ""), 16),
     amt = Math.round(2.55 * percent),
     R = (num >> 16) + amt,
     B = (num >> 8 & 0x00FF) + amt,
     G = (num & 0x0000FF) + amt;
 return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 + (B < 255 ? B < 1 ? 0 : B : 255) * 0x100 + (G < 255 ? G < 1 ? 0 : G : 255)).toString(16).slice(1);
}