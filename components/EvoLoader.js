import { BaseComponent, Events } from './BaseComponent.js';

const tpl = `<div id="evoloader" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
<div class="modal-dialog modal-sm" style="width: 200px;">
 <div class="modal-content">
  <div class="modal-body">
   <div class="spinner-border text-danger" role="status">
    <span class="visually-hidden">Loading...</span>
   </div>
   <span><slot name="label">Loading...</slot></span>
  </div>
 </div>
</div>
</div>`;

export default class EvoLoader extends BaseComponent {
 #element = null;
 #component = null;
 #params = {
  label: 'Loading...',
 }
 onOpened() {
  this.emit('onOpened', {
   detail: {}
  })
 }
 onClosed() {
  this.emit('onClosed', {
   detail: {}
  })
 }
 events = new Events(this); 
 constructor(id) {
  super();
  this.#element = this.setup(id, tpl, 'evoloader');
  this.#component = new bootstrap.Modal(`#${id}`);
  this.#element.addEventListener('shown.bs.modal', e => this.onOpened());
  this.#element.addEventListener('hidden.bs.modal', e => this.onClosed());
 }
 async #_prepare(params) {
  let theInstance = null;
  const label = this.#element.getElementsByTagName('slot').label;
  this.#params.label = 'Loading...';
  if (params.label !== undefined) this.#params.label = params.label;
  label.innerHTML = this.#params.label;
  theInstance = this.#element;
  return theInstance;
 }
 async show(params = {}) {
  await this.#_prepare(params);
  const modal = this.#component;
  modal.show(); 
 }
 async hide() {
  await tick();
  let modal = this.#component;
  if (modal._isTransitioning) {
   modal._isShown = true;
   modal._isTransitioning = false;
  } 
  modal.hide();
 }
}