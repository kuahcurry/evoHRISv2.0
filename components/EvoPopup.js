import { BaseComponent, Events } from './BaseComponent.js';

const tpl = `<div id="evopopup" class="modal fade" data-bs-backdrop="false" data-bs-keyboard="false" tabindex="-1" aria-labelledby="superPopupLabel" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title">
     <slot name="title">Popup title</slot>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
   </div>
   <div class="modal-body p-1 ps-2">
    <slot name="content">Popup content goes here.</slot>
   </div>
   <slot name="actions"></slot>
  </div>
</div>
</div>`;

export default class EvoPopup extends BaseComponent {
 #element = null;
 #component = null;
 #child = null;
 #size = '';
 #sizes = {small: 'modal-sm', medium: '', large: 'modal-lg', xl: 'modal-xl', fullscreen: 'modal-fullscreen'};
 #width = 'auto';
 #height = 'auto';
 onShown(childInstance) {
  this.emit('onOpened', {
   detail: {children: childInstance}
  })
 }
 onHidden(childInstance) {
  this.emit('onClosed', {
   detail: {children: childInstance}
  })
 }
 events = new Events(this); 
 constructor(id) {
  super();
  this.#element = this.setup(id, tpl, 'evopopup');
  this.#component = new bootstrap.Modal(`#${id}`);
  this.#element.addEventListener('shown.bs.modal', event => {
   const child = this.#child;
   if (child !== null) this.onShown(child);
  })
  this.#element.addEventListener('hidden.bs.modal', event => {
   const child = this.#child;
   if (child == null) return false;
   if (document[child.id] !== undefined) delete document[child.id];
   if (window.customElements[child.componentId] !== undefined) delete window.customElements[child.componentId];
   if (child !== null) this.onHidden(child);
  })
  this.#element.querySelector('.btn-close').addEventListener('click', event => {
   event.preventDefault();
   let modal = this.#component;
   modal.hide();
  })
 }
 async #_prepare(options, params) {
  let theInstance = null;
  const actions = this.#element.getElementsByTagName('slot').actions;
  actions.innerHTML = '';
  this.#element.querySelector('.modal-dialog').setAttribute('class', 'modal-dialog modal-dialog-scrollable');
  if (options.size !== undefined) this.#size = (this.#sizes[options.size] == undefined ? '' : this.#sizes[options.size]);  
  if (this.#size !== '') {
   const modalDialog = this.#element.querySelector('.modal-dialog');
   modalDialog.classList.add(this.#size);
  }
  this.#element.querySelector('.modal-content').removeAttribute('style');
  if (options.width !== undefined) this.#width = options.width;
  if (this.#width !== 'auto') {
   const modalContent = this.#element.querySelector('.modal-content');
   modalContent.style = `width: ${this.#width}`;
  } 
  const title = this.#element.getElementsByTagName('slot').title;
  const content = this.#element.getElementsByTagName('slot').content;
  title.innerHTML = params.title;
  if (typeof params.content == 'object') {
   clearContent(content);
   if (params.content.type == 'component') {
    const nmId = params.content.container;
    let objCmp = {};
    let target = document.createElement(nmId);
    content.appendChild(target);
    window.customElements[nmId] = typeof params.content.component == 'object' ? params.content.component.name : params.content.component;
    objCmp[nmId] = new window.customElements[nmId](nmId, typeof params.content.component == 'object' ? params.content.component.props : {});
    theInstance = objCmp[nmId];
   }else if (params.content.type == 'module') {
    let nm = params.content.name.split('/');
    let nmId = nm[0];
    if (nm.length > 1) nmId = nm[1];
    content.setAttribute('id', nmId);
    await loadModule(params.content.name, `#${nmId}`);
    content.removeAttribute('id', nmId);
    theInstance = document[nmId];
   }
  }else {
   content.innerHTML = params.content;
   theInstance = this.#element;
  }
  if (params.buttons !== undefined) {
   let buttons = [];
   params.buttons.forEach(button => {
    buttons.push(`<button id="btn-${button.label.toLowerCase()}" type="button" class="btn btn-sm ${button.class}">${button.label}</button>`);
   })
   actions.innerHTML = `<div class="modal-footer">${buttons.join('\n')}</div>`;
   let btn = actions.getElementsByTagName('button');
   params.buttons.forEach((button, index) => {
    btn[index].addEventListener('click', (e) => {
     button.onClick(e);
     btn[index].removeEventListener('click', (e) => button.onClick(e));
    })
   })
  }
  return theInstance;
 }
 async open(options, params) {
  this.#child = await this.#_prepare(options, params);
  const modal = this.#component;
  modal.show(); 
 }
 close() {
  let modal = this.#component;
  if (modal._isTransitioning) {
   modal._isShown = true;
   modal._isTransitioning = false;
  } 
  modal.hide(); 
 }
}