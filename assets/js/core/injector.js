const getBrowserType = () => {
 const test = regexp => {
  return regexp.test(navigator.userAgent);
 };
 if (test(/opr\//i) || !!window.opr) {
  return 'Opera';
 }else if (test(/edg/i)) {
  return 'Microsoft Edge';
 }else if (test(/chrome|chromium|crios/i)) {
 return 'Google Chrome';
 }else if (test(/firefox|fxios/i)) {
  return 'Mozilla Firefox';
 }else if (test(/safari/i)) {
  return 'Apple Safari';
 }else if (test(/trident/i)) {
  return 'Microsoft Internet Explorer';
 }else if (test(/ucbrowser/i)) {
  return 'UC Browser';
 }else if (test(/samsungbrowser/i)) {
  return 'Samsung Browser';
 }else {
  return 'Unknown browser';
 }
}
const loadModule = async (file, target = '#module-container', type = 'file', reload = false) => {
 let res = false; 
 let targetTag = 'div';
 let response = null;
 let fullname = file.split('/');
 let filename = fullname[fullname.length - 1];
 const flag = file.split('#');
 const filepath = flag[0];
 if (target.startsWith('<') == false && target.endsWith('>') == false) {
  let targetElement = document.querySelector(target);
  if (type == 'part') filename = flag[1];
  const moduleElement = document.createElement(filename);
  if (targetElement == null) {
   console.error(`Target element ${target} not found`);
   return false;
  }
  targetElement.innerHTML = '';
  targetElement.appendChild(moduleElement);
 }else {
  targetTag = 'custom';
 }
 let elem = document.querySelector(filename);
 if (targetTag == 'custom') {
  const tagName = target.replace(/</i, '').replace(/>/i, '');
  elem = document.querySelector(tagName);
 }
 if (elem == null) return false;
 elem.innerHTML = 'Loading module...';
 try {
  response = reload ? await fetch(`./${filepath}.html`, {cache: 'reload'}) : await fetch(`./${filepath}.html`);
 } catch (err) {
  console.error(err.message);
 }
 if (response) {
  elem.innerHTML = '';
  const text = await response.text();
  const parser = new DOMParser();
  const doc = parser.parseFromString(text, 'text/html');
  let css = null;
  if (doc.querySelector('style') !== null) css = doc.querySelector('style').textContent;
  const style = document.createElement('style');
  style.textContent = css;
  elem.appendChild(style);
  let html = null;
  if (type == 'part') {
   if (doc.querySelector(`template#${flag[1]}`) !== null && flag.length > 1) html = doc.querySelector(`template#${flag[1]}`).content.cloneNode(true);
  }else if (type == 'file') {
   if (doc.querySelector('template') !== null) html = doc.querySelector('template').content.cloneNode(true);
  }
  elem.appendChild(html);
  let code = null;
  if (document.readyState === "loading") {
   document.addEventListener("DOMContentLoaded", console.info(`loading module ${filename} is on process...`));
  }else {
   if (doc.querySelector('script') !== null) code = doc.querySelector('script').textContent;
   const scr = document.createElement('script');
   scr.setAttribute('type', 'module');
   if (getBrowserType().toLocaleLowerCase().includes('chrome')) scr.defer = true;
   if (getBrowserType().toLocaleLowerCase().includes('firefox')) scr.setAttribute('defer', true);
   scr.textContent = code;
   elem.after(scr);
   elem.replaceWith(...elem.childNodes);
   res = true;
  }
 } else {
  console.error(`HTTP Response Code: ${response?.status}`)
 }
 let promise = new Promise((resolve, reject) => {
  setTimeout(() => res ? resolve(true) : reject(false), 300);
 });
 let result = await promise;
 return result;
}
const renderTemplate = (tpl, datasource, dataset) => {
 const parser = new DOMParser();
 const doc = parser.parseFromString(tpl, 'text/html');
 const keys = Object.keys(datasource);
 const a = doc.querySelector('a');
 a.dataset.pasien = JSON.stringify(dataset);
 keys.forEach(v => {
  let c = a.querySelector(`[id="${v}"]`);
  if (c == null) {
   if (datasource[v] !== '') a.classList.add(datasource[v]);
  }else {
   c.textContent = datasource[v];
  }
 });
 return a;
}
const clearContent = (parent) => {
 while (parent.firstChild) {
  parent.removeChild(parent.firstChild);
 }
 return true;
}
const tick = async (time = 100) => {
 let timer = await new Promise((resolve, reject) => setTimeout(resolve, time));
 clearTimeout(timer);
 return true;
}