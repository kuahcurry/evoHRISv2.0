import { BaseComponent, Events } from './BaseComponent.js';

const tpl = `<div id="evoselector" data-role="selector" class="m-0 p-0 position-relative" style="width: 100%;">
 <input type="text" class="form-control form-control-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-type="select" data-field="" data-text="" data-value="" />
 <a href="#!selectorclose" class="d-none"><span class="fa-solid fa-times-circle"></span></a>
 <span class="fa-solid fa-chevron-down"></span>
 <ul class="dropdown-menu" style="width: 100%; height: auto;position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 31px);overflow-y: scroll;"></ul>
</div>`;

export default class EvoSelector extends BaseComponent {
 #element;
 #config = {};
 #header = [];
 #options = [];
 #changeInterval = null;
 onTyped(e) {
  this.emit('typed', {
   detail: {value: e.target.value}
  })
 }
 onSelected(e) {
  const value = {value: e.value};
  const values = e.values == undefined ? {} : {values: JSON.parse(e.values)};
  let combines = value;
  if (Object.keys(values).length > 0) combines = {...combines, ...values};
  this.emit('selected', {
   detail: combines
  })
 }
 onClear(e) {
  this.emit('cleared');
 }
 events = new Events(this); 
 constructor(id, items = []) {
  super();
  this.#element = this.setup(id, tpl, 'evoselector', 'input', 'visual');
  const elem = this.getInitialElement();
  this.#config = {
   captureTypedOn: this.getAttr(elem, 'captureTypedOn') == null ? 'change' : this.getAttr(elem, 'captureTypedOn'),
   clearable: this.getAttr(elem, 'clearable') == null ? true : JSON.parse(this.getAttr(elem, 'clearable')),
   dropdownMode: this.getAttr(elem, 'dropdownMode') == null ? 'list' : this.getAttr(elem, 'dropdownMode'),
   dropdownWidth: this.getAttr(elem, 'dropdownWidth') == null ? '100%' : this.getAttr(elem, 'dropdownWidth'),
   dropdownHeight: this.getAttr(elem, 'dropdownHeight') == null ? '350px' : this.getAttr(elem, 'dropdownHeight'),
   eventDelay: this.getAttr(elem, 'eventDelay') == null ? 500 : this.getAttr(elem, 'eventDelay'),
   height: this.getAttr(elem, 'height') == null ? '31px' : this.getAttr(elem, 'height'),
   placeholder: this.getAttr(elem, 'placeholder') == null ? '' : this.getAttr(elem, 'placeholder'), 
   readonly: this.getAttr(elem, 'readonly') == null ? false : JSON.parse(this.getAttr(elem, 'readonly')),
   searchable: this.getAttr(elem, 'readonly') == null ? false : !JSON.parse(this.getAttr(elem, 'readonly')),
   width: this.getAttr(elem, 'width') == null ? '100%' : this.getAttr(elem, 'width')
  };
  const isItArray = Array.isArray(items);
  if (isItArray == false) this.#header = items.header;
  const options = (elem.querySelectorAll('option')).length > 0 ? elem.querySelectorAll('option') : (isItArray ? items : (items.options == undefined ? [] : items.options));
  this.#options = Array.from(options);
  this.#_init();
 }
 #_upEvent = async (e) => {
  if (this.#config.captureTypedOn == 'enter') {
   if (e.keyCode == 13) await this.onTyped(e);
  }else if (this.#config.captureTypedOn == 'change') {
   clearInterval(this.#changeInterval)
   const that = this;
   this.#changeInterval = setInterval(async function() {
    clearInterval(that.#changeInterval);
    await that.onTyped(e);
   }, this.#config.eventDelay);
  }
 }
 #_setHeader = async () => {
  const alreadyThere = this.#element.querySelector('ul[class^="grid-head"]');
  if (alreadyThere !== null) return false;
  const pul = this.#element.querySelector('.dropdown-menu');
  pul.setAttribute('style', `width: ${this.#config.dropdownWidth}; height: auto;position: absolute; inset: 0px auto auto 0px; margin: 0px; padding: 5px; transform: translate(0px, 31px);overflow-y: hidden;`);
  const ul = document.createElement('ul');
  ul.setAttribute('class', 'grid-head list-group list-group-horizontal-sm');
  ul.setAttribute('style', 'overflow-y: scroll;');
  for (let i = 0; i < this.#header.length; i++) {
   const li = document.createElement('li');
   if (this.#header[i].width == undefined) {
    li.setAttribute('class', 'list-group-item flex-fill');
   }else {
    li.setAttribute('class', 'list-group-item');
    li.setAttribute('style', `width: ${this.#header[i].width}`);
   }
   li.innerHTML = this.#header[i].text;
   ul.appendChild(li);
  }
  pul.appendChild(ul);
  const thebody = document.createElement('div');
  thebody.setAttribute('class', 'grid-body');
  thebody.setAttribute('style', 'max-height: 200px;overflow-y: scroll;');
  pul.appendChild(thebody);
 }
 #_setOption(item) {
  let values = '';
  if (item.values !== undefined) values = ` data-values='${JSON.stringify(item.values)}'`; 
  const mode = this.#config.dropdownMode;
  if (mode == 'list') {
   let li = document.createElement('li');
   li.innerHTML = `<a class="dropdown-item" href="#" data-text="${item.text}" data-value="${item.value}"${values}>${item.text}</a>`;
   return li;
  }else if (mode == 'grid') {
   const tblgrid = this.#element.querySelector('div[class^="grid-body"]');
   const ul = document.createElement('ul');
   ul.setAttribute('class', 'list-group list-group-horizontal-sm')
   ul.setAttribute('data-text', item.text);
   ul.setAttribute('data-value', item.value);
   ul.setAttribute('data-values', JSON.stringify(item.values));
   for (let i = 0; i < this.#header.length; i++) {
    const li = document.createElement('li');
    if (this.#header[i].width == undefined) {
     li.setAttribute('class', 'list-group-item flex-fill');
    }else {
     li.setAttribute('class', 'list-group-item');
     li.setAttribute('style', `width: ${this.#header[i].width}`);
    }
    li.innerHTML = item.values[this.#header[i].value] == undefined ? `#${tblgrid.children.length + 1}` : item.values[this.#header[i].value];
    ul.appendChild(li);
   }
   return ul;
  }
 }
 #_setOptions(items) {
  const mode = this.#config.dropdownMode;
  if (mode == 'list') {
   const ul = this.#element.querySelector('.dropdown-menu');
   let i = document.createElement('li');
   Array.from(ul.children).map(v => ul.removeChild(v));
  }else if (mode == 'grid') {
   const tblgrid = this.#element.querySelector('div[class^="grid-body"]');
   Array.from(tblgrid.children).map(v => tblgrid.removeChild(v));
  }
  if (items.length > 0) {
   items.forEach(o => {
    if (mode == 'list') {
     const ul = this.#element.querySelector('.dropdown-menu');
     const li = this.#_setOption(o);
     ul.appendChild(li);
    }else if (mode == 'grid') {
     const tblgrid = this.#element.querySelector('div[class^="grid-body"]');
     const list = this.#_setOption(o);
     tblgrid?.appendChild(list);
    }
   })
  }else {
   if (mode == 'list') {
    const ul = this.#element.querySelector('.dropdown-menu');
    let i = document.createElement('li');
    i.innerHTML = `<a class="dropdown-item" href="#" data-value="-" data-text="" inert>Belum ada item</a>`;
    ul.appendChild(i);
   }else if (mode == 'grid') {
    const tblgrid = this.#element.querySelector('div[class^="grid-body"]');
    const list = document.createElement('ul');
    list.setAttribute('class', 'list-group list-group-horizontal-sm');
    list.setAttribute('data-text', '');
    list.setAttribute('data-value', '-');
    const item = document.createElement('li');
    item.setAttribute('class', 'list-group-item flex-fill');
    item.innerHTML = 'Belum ada item';
    list.appendChild(item);
    tblgrid?.appendChild(list);
   }
  }
  const triggerElem = mode == 'list' ? '.dropdown-menu > li a[class^="dropdown-item"]' : 'div[class^="grid-body"] > ul';
  const dropdownList = this.#element.querySelectorAll(triggerElem);
  dropdownList.forEach(a => a.removeEventListener('click', e => {}));
  dropdownList.forEach(a => {
   a.addEventListener('click', e => {
    let dataset = e.target.dataset;
    const mode = this.#config.dropdownMode;
    if (mode == 'grid') dataset = e.target.parentElement.dataset;  
    if (dataset.value == '' || dataset.value == '-') return false;
    const input = this.#element.querySelector('input[class$="dropdown-toggle show"]') || this.#element.querySelector('input.dropdown-toggle') || this.#element.querySelector('input');
    if (input) {
      input.value = dataset.text;
      input.dataset.text = dataset.text;
      input.dataset.value = dataset.value;
      input.focus();
    }
    this.onSelected(dataset);
   })
  })
 }
 #_init() {
  const input = this.#element.querySelector('input[class$="dropdown-toggle"]');
  const btnclose = this.#element.querySelector('a[href="#!selectorclose"]');
  if (this.#config.placeholder !== '') input.setAttribute('placeholder', this.#config.placeholder);
  if (this.#config.readonly) {
   input.setAttribute('readonly', true);
  }else {
   input.removeAttribute('readonly');
  }
  if (this.#config.height) this.#element.style.height = this.#config.height;
  if (this.#config.width) this.#element.style.width = this.#config.width;
  input.addEventListener('focus', e => {
   if (this.#config.clearable) {
    if (e.target.value !== '') {
     btnclose.classList.remove('d-none');
    }else {
     btnclose.classList.add('d-none');
    }
   }
  })
  const handleFilter = (e) => {
   const mode = this.#config.dropdownMode;
   if (mode == 'grid') this.#_setHeader();
   const q = (e.target.value || '').toLowerCase().trim();
   let filteredItems = this.#options.filter(o => (o.text || '').toLowerCase().includes(q));
   this.#_setOptions(filteredItems);
   const ul = this.#element.querySelector('.dropdown-menu');
   if (ul && !ul.classList.contains('show')) {
     ul.classList.add('show');
   }
  };
  input.addEventListener('input', handleFilter);
  input.addEventListener('keyup', e => this.#_upEvent(e));
  btnclose.addEventListener('click', e => {
   this.#_reset();
   this.onClear();
  })
  const mode = this.#config.dropdownMode;
  if (mode == 'list') {
   const ul = this.#element.querySelector('.dropdown-menu');
   ul.style.maxHeight = this.#config.dropdownHeight;
   ul.style.width = this.#config.dropdownWidth;
  }
  if (mode == 'grid') this.#_setHeader();
  const items = this.#options == undefined ? [] : this.#options;
  this.#_setOptions(items);
  let firsta = this.#element.querySelector('.dropdown-menu > li a[class^="dropdown-item"]');
  if (mode == 'grid') {
   firsta = this.#element.querySelector('div[class^="grid-body"] > ul > li');
  }
  if (firsta && firsta.dataset.value != '-') {
    input.value = firsta.textContent;
    input.dataset.text = firsta.dataset.text;
    input.dataset.value = firsta.dataset.value;
  }
  try {
    Object.defineProperty(this.#element, 'value', {
      get: () => this.value,
      set: (val) => { this.value = val; },
      configurable: true
    });
  } catch (err) {}
 }
 #_reset(inputText = null) {
  const input = this.#element.querySelector('input[class$="dropdown-toggle"]');
  if (input !== null) input.value = '';
  if (input !== null) input.dataset.text = '';
  if (input !== null) input.dataset.value = '-';
  const btnclose = this.#element.querySelector('a[href="#!selectorclose"]');
  if (this.#config.clearable) btnclose.classList.add('d-none');
 }
 addOption(item) {
  const mode = this.#config.dropdownMode;
  if (mode == 'grid') this.#_setHeader();
  this.#_setOption(item);
 }
 addOptions(items) {
  const mode = this.#config.dropdownMode;
  if (mode == 'grid') this.#_setHeader();
  this.#options = Array.isArray(items) ? Array.from(items) : [];
  this.#_setOptions(items);
 }
 clearOptions() {
  this.#options = [];
  this.#_setOptions([]);
 }
 set value(data) {
  const mode = this.#config.dropdownMode;
  let aliul = this.#element.querySelectorAll('.dropdown-menu > li a[class^="dropdown-item"]');
  if (mode == 'grid') aliul = this.#element.querySelectorAll('div[class^="grid-body"] > ul');
  const input = this.#element.querySelector('input.dropdown-toggle') || this.#element.querySelector('input[class$="dropdown-toggle"]');
  const targetVal = (data == null) ? '' : String(data).trim().toLowerCase();
  const matched = Array.from(aliul).find(a => (a.dataset.value || '').trim().toLowerCase() === targetVal);
  if (matched && input) {
   input.value = matched.dataset.text || matched.textContent;
   input.dataset.text = matched.dataset.text || matched.textContent;
   input.dataset.value = matched.dataset.value;
  } else if (!data || data === '-' || data === '') {
   this.#_reset();
  }
  return true;
 }
 get value() {
  const input = this.#element.querySelector('input[class$="dropdown-toggle"]');
  return input?.dataset.value;
 }
}