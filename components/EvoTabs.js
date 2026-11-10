import { BaseComponent, Events, lightenDarkenColor } from './BaseComponent.js';

const tpl = `
  <div id="evotabs" class="m-0 p-0 card">
    <ul class="nav nav-tabs" role="tablist"></ul>
    <div class="tab-content p-3 border-start border-bottom" style="overflow-y: scroll; overflow-x: hidden;min-height: 100%; max-height: 85vh;"></div>
  </div>
`;
// const tpl = `<div id="evotabs" class="m-0 p-0"><ul class="nav nav-tabs" role="tablist"></ul><div class="tab-content p-3 border-start border-bottom" style="overflow-y: scroll; overflow-x: hidden;min-height: 100px; max-height: 70vh;"></div></div>`;

export default class EvoTabs extends BaseComponent {
 #element = null;
 #component = null;
 #child = null;
 #lazyLoad = true;
 #activeOnly = true;
 #alltabs = [];
 #width = 'auto';
 #closeAble = false;
 #tabButtons = [];
 #tabContents = [];
 onCreated(childInstance) {
  this.emit('tabCreated', {
   detail: {children: childInstance}
  })
 }
 onError(childInstance) {
  this.emit('tabError', {
   detail: {children: childInstance}
  })
 }
 async onChange(childInstance) {
  if (this.#lazyLoad) {
   const from = childInstance.relatedTarget;
   const to = childInstance.target.dataset;
   if (this.#activeOnly && from?.dataset?.bsModule !== undefined && from?.dataset?.bsContainer !== undefined) {
    if (from !== null) {
     const fromTag = (from.dataset.bsContainer).replace(/</i, '').replace(/>/i, '');
     const prev = document.querySelector(from.dataset.bsTarget);
     const tag = document.createElement(fromTag);
     prev.textContent = '';
     prev.appendChild(tag);
    } 
   }
   if (to.bsContainer !== undefined) await loadModule(to.bsModule, to.bsContainer, 'file', true);
   this.#child = document[to.bsTag];
   if (from !== null) {
    const defaultStyle = from.dataset.bsStyle;
    if (defaultStyle !== undefined) {
     const leftActiveTab = document.querySelector(`button[data-bs-tag="${from.dataset.bsTag}"]`);
     leftActiveTab?.setAttribute('style', defaultStyle);
    }
   }
   const activeStyle = to.bsActiveStyle;
   if (activeStyle !== undefined) {
    const activeTab = document.querySelector(`button[class$="active"][data-bs-tag="${to.bsTag}"]`);
    activeTab?.setAttribute('style', activeStyle);
   }
  }
  this.emit('tabChange', {
   detail: {children: childInstance}
  })
 }
 async onChanged(childInstance) {
  this.emit('tabChanged', {
   detail: {children: childInstance}
  })
 }
 onDestroyed(childInstance) {
  this.emit('tabDestroyed', {
   detail: {children: childInstance}
  });
 }
 events = new Events(this); 
 constructor(id, tabs = []) {
  super();
  const fromBase = this.setup(id, tpl, 'evotabs', 'ul', 'visual');
  if (fromBase == false) return {};
  this.#element = fromBase;
  this.#element.addEventListener('show.bs.tab', e => this.onChange(e));
  this.#element.addEventListener('shown.bs.tab', e => this.onChanged(e));
  this.#component = new bootstrap.Tab(`#${id}`);
  let alltabs = tabs;
  if (tabs.length == 0) {
   const elem = this.getInitialElement();
   const elemtabs = elem.querySelectorAll('tab');
   this.#lazyLoad = this.getAttr(elem, 'data-lazy-load') == undefined ? this.#lazyLoad : JSON.parse(this.getAttr(elem, 'data-lazy-load'));
   this.#activeOnly = this.getAttr(elem, 'data-active-only') == undefined ? this.#activeOnly : JSON.parse(this.getAttr(elem, 'data-active-only'));
   if (this.#activeOnly) this.#lazyLoad = true;   
   const arrtabs = Array.from(elemtabs).map(t => {
    const type = this.getAttr(t, 'type') == null ? 'text' : this.getAttr(t, 'type');
    let content = t.innerHTML;
    if (type == 'module') {
     const name = this.getAttr(t.children[0], 'name');
     const container = t.children[0]?.tagName?.toLowerCase();
     content = {name: name, container: `<${container}>`};
    } 
    return {id: this.getAttr(t, 'id'), tag: this.getAttr(t, 'tag'), label: this.getAttr(t, 'label'), type: type, content: content, closeable: JSON.parse(this.getAttr(t, 'closeable'))};
   });
   alltabs = arrtabs;
  }else {
   const elem = this.getInitialElement();
   this.#lazyLoad = this.getAttr(elem, 'data-lazy-load') == undefined ? this.#lazyLoad : JSON.parse(this.getAttr(elem, 'data-lazy-load'));
   this.#activeOnly = this.getAttr(elem, 'data-active-only') == undefined ? this.#activeOnly : JSON.parse(this.getAttr(elem, 'data-active-only'));
  }
  this.#_prepare(alltabs);
 }
 async #_removeTab(targetTab) {
  let tabbtn = this.#element.querySelector(`li[id="${targetTab}-li"]`);
  let tabcontent = this.#element.querySelector(`div[id="${targetTab}"]`);
  tabbtn.remove();
  tabcontent.remove();
  const triggerEl = this.#element.querySelector(`.nav-tabs li:first-child button`);
  if (triggerEl !== null) triggerEl.click();
  let found = this.#tabButtons.findIndex(tb => tb.id == targetTab);
  if (found > -1) this.#tabButtons.splice(found, 1);
  found = this.#tabContents.findIndex(ct => ct.id == targetTab);
  if (found > -1) this.#tabContents.splice(found, 1);
  const child = this.#child;
  if (child !== null && child !== undefined) {
   if (document[child.id] !== undefined) delete document[child.id];
   if (window.customElements[child.id] !== undefined) delete window.customElements[child.id];
   this.onDestroyed(child);
  }
  const foundTab = this.#alltabs.findIndex(t => t == targetTab);
  if (foundTab > -1) this.#alltabs.splice(foundTab, 1);
  return true;
 }
 async #_addCloseEvent(tab) {
  const closeHandler = async (e) => {
   e.preventDefault();
   e.stopPropagation();
   return await this.#_removeTab(e.target.dataset.tab);
  }
  const btnclose = this.#element.querySelector(`.nav-link[id="${tab.id}-tab"] > a[href="#!tabclose"]`);
  if (btnclose !== null) btnclose.addEventListener('click', closeHandler); 
 }
 async #_createTab(tab, x, active) {
  let btnclose = '';
  let visualStyle = '';
  let visualActiveStyle = '';
  if (tab.color !== undefined) {
   visualStyle = `color: ${tab.color};`;
   visualActiveStyle = `color: ${lightenDarkenColor(tab.color, 2)};`;
  } 
  if (tab.bgcolor !== undefined) {
   visualStyle += `background-color: ${tab.bgcolor};`;
   visualActiveStyle += `background-color: ${lightenDarkenColor(tab.bgcolor, -6)};`;
  } 
  if (tab.closeable) btnclose = `<a href="#!tabclose" class="close" data-tab="${tab.id}"><span class="fa-solid fa-times-circle"></span></a>`; 
  let btn = `<li class="nav-item tab-btn position-relative" role="presentation" id="${tab.id}-li"><button class="nav-link${active ? ' active' : ''}" style="${visualStyle}" id="${tab.id}-tab" data-bs-toggle="tab" data-bs-target="#${tab.id}" data-bs-tag="${tab.tag}" data-bs-style="${visualStyle}" data-bs-active-style="${visualActiveStyle}" type="button" role="tab" aria-controls="${tab.id}" aria-selected="${active}">${tab.label} ${btnclose}</button></li>`;
  if (tab.type == 'module' && (this.#lazyLoad || this.#activeOnly)) btn = `<li class="nav-item tab-btn position-relative" role="presentation" id="${tab.id}-li"><button class="nav-link${active ? ' active' : ''}" style="${visualStyle}" id="${tab.id}-tab" data-bs-toggle="tab" data-bs-target="#${tab.id}" data-bs-tag="${tab.tag}" data-bs-style="${visualStyle}" data-bs-active-style="${visualActiveStyle}" data-bs-module="${tab.content.name}" data-bs-container="${tab.content.container}" type="button" role="tab" aria-controls="${tab.id}" aria-selected="${active}">${tab.label} ${btnclose}</button></li>`;
  let ctn = `<div class="tab-pane fade${active ? ' show active' : ''}" id="${tab.id}" role="tabpanel" aria-labelledby="${tab.id}-tab" tabindex="${x}">${tab.content}</div>`;
  if (tab.type == 'module') ctn = `<div class="tab-pane fade${active ? ' show active' : ''}" id="${tab.id}" role="tabpanel" aria-labelledby="${tab.id}-tab" tabindex="${x}">${tab.content.container}</div>`;
  return {id: tab.id, btn: btn, ctn: ctn};
 }
 async #_prepare(tabs) {
  const buttons = this.#element.querySelector('.nav-tabs');
  const contents = this.#element.querySelector('.tab-content');
  const elem = this.getInitialElement();
  if (this.getAttr(elem, 'height') !== null) contents.style.maxHeight = `${this.getAttr(elem, 'height')}`;
  if (this.getAttr(elem, 'minheight') !== null) contents.style.minHeight = `${this.getAttr(elem, 'minheight')}`;
  const existedClass = 'tab-content border-start border-bottom';
  let addedClass = ' p-3';
  if (this.getAttr(elem, 'class') !== null) addedClass = ' '+this.getAttr(elem, 'class');
  contents.setAttribute('class', `${existedClass}${addedClass}`);
  buttons.innerHTML = '';
  contents.innerHTML = '';
  if (tabs.length == 0) return false;
  let x = 0;
  const anyActived = tabs.findIndex(i => i.active == true);
  for await (const tab of tabs) {
   const active = (x == 0 && anyActived == -1) ? true : (anyActived == x ? true : false); 
   await this.addTab(tab, active);
   x++;
  }
 }
 async #_setToActive(id) {
  const triggerEl = this.#element.querySelector(`.nav-tabs > li > button[data-bs-target="#${id}"]`);
  triggerEl.click();
 }
 async addTab(newTab, activated = false) {
  let newTabIndex = 0;
  const found = this.#alltabs.findIndex(i => i == newTab.id);
  if (found > -1) {
   this.onError(this.#child);
   return false;
  }
  const buttons = this.#element.querySelector('.nav-tabs');
  const contents = this.#element.querySelector('.tab-content');
  if (buttons.children.length > 0) newTabIndex = buttons.children.length;
  const created = await this.#_createTab(newTab, newTabIndex, false);
  const btn = this.createNewElement(created.btn, 'li[class^="nav-item"]');
  const ctn = this.createNewElement(created.ctn, 'div[class^="tab-pane"]');
  buttons.appendChild(btn);
  contents.appendChild(ctn);
  if (newTab.type == 'module') {
   let nm = newTab.content.name.split('/');
   let nmId = nm[nm.length - 1];
   if (this.#lazyLoad == false && this.#activeOnly == false) {
    await loadModule(newTab.content.name, newTab.content.container);
   }
   this.#child = document[nmId];
  }
  const iscloseable = newTab.closeable == undefined ? this.#closeAble : newTab.closeable; 
  if (iscloseable) await this.#_addCloseEvent(newTab); 
  if (activated) this.#_setToActive(newTab.id);
  this.#alltabs.push(newTab.id);
  await this.onCreated(this.#child);
  return true;
 }
 async addTabs(tabs, activatedTab = 0) {
  let x = 0;
  for await (const newtab of tabs) {
   let activated = false;
   if (activatedTab == x) activated = true;
   await this.addTab(newtab, activated);
   x++;
  }
  return true;
 }
 async removeTab(tabs) {
  const varType = typeof tabs == 'object' ? 'array' : 'string';
  if (varType == 'array') {
   for await (const tab of tabs) {
    this.#_removeTab(tab)
   }
  }else if (varType == 'string') {
   this.#_removeTab(tab);
  }
 }
 get tabs() {
  return this.#alltabs;
 }
}