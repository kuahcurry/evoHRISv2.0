import { BaseComponent, Events } from './BaseComponent.js';

const tpl = `<div id="evodialog" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="superDialogLabel" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title">
     <slot name="title">Dialog title</slot>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
   </div>
   <div class="modal-body p-1 ps-2">
    <slot name="content">Dialog content goes here.</slot>
   </div>
   <slot name="actions"></slot>
  </div>
</div>
</div>`;

export default class EvoDialog extends BaseComponent {
 #element = null;
 #component = null;
 #sizes = {small: 'modal-sm', medium: '', large: 'modal-lg', xl: 'modal-xl'};
 #icons = {info: 'fa-info-circle', question: 'fa-question-circle', warning: 'fa-exclamation-circle', success: 'fa-check-circle'};
 #colors = {info: 'bg-info', question: '', warning: 'bg-warning', success: 'bg-success'};
 #hexacolor = {info: '#27bcfd', question: '', warning: '#f5803e', success: '#00d27a'};
 #startTimer = false;
 #timerOut = null;
 #config = {
  size: 'modal-sm',
  width: 'auto',
  height: 'auto',
  color: {
   onheader: '',
   onleftborder: '',
   ontopborder: '',
   onrightborder: ''
  },
  modal: true,
  autoclose: false,
  autoCloseAfter: 1000,
  theme: 'onleftborder'
 }
 #params = {
  type: '',
  title: '',
  label: '',
  buttons: []
 }
 onShown() {
  this.emit('onOpened', {
   detail: {}
  })
 }
 onHidden() {
  this.emit('onClosed', {
   detail: {}
  })
 }
 events = new Events(this); 
 constructor(id) {
  super();
  this.#element = this.setup(id, tpl, 'evodialog');
  this.#component = new bootstrap.Modal(`#${id}`);
  this.#element.addEventListener('shown.bs.modal', event => {
  const component = this.#component;
   if (this.#startTimer) {
    this.#timerOut = setTimeout(() => {
     component.hide();
     this.onShown();
    }, this.#config.autoCloseAfter);
   }else {
    this.onShown();
   }
  })
  this.#element.addEventListener('hidden.bs.modal', event => {
   if (this.#startTimer) {
    this.#startTimer = false;
    clearTimeout(this.#timerOut);
    this.onHidden();
   }else {
    this.onHidden();
   }
  })
  this.#element.querySelector('.btn-close').addEventListener('click', event => {
   let modal = this.#component;
   modal.hide();
  })
 }
 async #_prepare(config, params) {
  let theInstance = null;
  const actions = this.#element.getElementsByTagName('slot').actions;
  actions.innerHTML = '';
  this.#config.color = {onheader: '', onleftborder: '', ontopborder: '', onrightborder: ''};
  if (params.type !== undefined) this.#params.type = params.type;
  if (params.title !== undefined) this.#params.title = params.title;
  if (params.label !== undefined) this.#params.label = params.label;
  if (params.buttons !== undefined) this.#params.buttons = params.buttons;
  if (config.modal !== undefined) this.#config.modal = config.modal;
  if (config.autoclose !== undefined) this.#config.autoclose = config.autoclose;
  if (config.size !== undefined) this.#config.size = this.#sizes[config.size] == undefined ? '' : this.#sizes[config.size];
  if (config.width !== undefined) this.#config.width = config.width;
  if (config.theme !== undefined) {
   this.#config.color[config.theme] = this.#colors[this.#params.type] == undefined ? '' : this.#colors[this.#params.type];
   this.#config.theme = config.theme;
  }

  if (this.#config.autoclose) this.#startTimer = true;
  if (this.#config.size !== '') {
   const modalDialog = this.#element.querySelector('.modal-dialog');
   modalDialog.classList.add(this.#config.size);
  }
  let modalContent = this.#element.querySelector('.modal-content');
  modalContent.removeAttribute('style');
  if (this.#config.width !== 'auto') {
   modalContent.style = `width: ${this.#config.width}`;
  } 
  let modalHeader = this.#element.querySelector('.modal-header');
  modalHeader.classList.remove('bg-info', 'bg-warning', 'bg-success');
  if (this.#config.theme == 'onheader' && this.#config.color[this.#config.theme] !== '') {
   modalHeader = this.#element.querySelector('.modal-header');
   modalHeader.classList.add(this.#config.color[this.#config.theme]);
  }
  if (this.#config.theme == 'ontopborder' && this.#config.color[this.#config.theme] !== '') {
   modalContent = this.#element.querySelector('.modal-content');
   modalContent.style = `border-top: solid 10px ${this.#hexacolor[params.type]}`;
  }
  if (this.#config.theme == 'onleftborder' && this.#config.color[this.#config.theme] !== '') {
   modalContent = this.#element.querySelector('.modal-content');
   modalContent.style = `border-left: solid 10px ${this.#hexacolor[params.type]}`;
  }
  if (this.#config.theme == 'onrightborder' && this.#config.color[this.#config.theme] !== '') {
   modalContent = this.#element.querySelector('.modal-content');
   modalContent.style = `border-right: solid 10px ${this.#hexacolor[params.type]}`;
  }
  const title = this.#element.getElementsByTagName('slot').title;
  const content = this.#element.getElementsByTagName('slot').content;
  title.innerHTML = `<span class="fa-solid ${this.#icons[this.#params.type]}"></span> ${this.#params.title}`;
  content.innerHTML = this.#params.label;
  theInstance = this.#element;
  let buttons = [];
  this.#params.buttons.forEach(button => {
   buttons.push(`<button id="btn-${button.label.toLowerCase()}" type="button" class="btn btn-sm ${button.class}">${button.label}</button>`);
  })
  actions.innerHTML = `<div class="modal-footer">${buttons.join('\n')}</div>`;
  let btn = actions.getElementsByTagName('button');
  this.#params.buttons.forEach((button, index) => {
   btn[index].addEventListener('click', (e) => {
    button.onClick(e);
    btn[index].removeEventListener('click', (e) => button.onClick(e));
   })
  })
  return theInstance;
 }
 async open(config, params) {
  await this.#_prepare(config, params);
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