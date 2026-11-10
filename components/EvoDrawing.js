import { BaseComponent, Events } from './BaseComponent.js';

const tpl = `<div id="evodrawing" style="width: 480px;height: 320px;margin:0;padding:0px;"><canvas width="480" height="320" style="z-index: 1;border: solid 1px #cecece;" data-type="canvas" data-field="" data-text="" data-value=""></canvas>
<div class="toolbar"><a class="btn btn-sm btn-primary fs--1" href="#!btn-drawing" data-mode="pen" role="button"><i class="fa-solid fa-pencil"></i></a> <a class="btn btn-sm btn-primary fs--1" href="#!btn-drawing" data-mode="eraser" role="button"><i class="fa-solid fa-eraser"></i></a>
 <a class="btn btn-sm btn-primary fs--1" href="#!btn-drawing" data-mode="trash" role="button"><i class="fa-solid fa-trash"></i></a> <a class="btn btn-sm btn-primary fs--1 float-end" href="#!btn-drawing" data-mode="upload" role="button"><i class="fa-solid fa-folder"></i></a><div class="float-none"></div>`;

export default class EvoDrawing extends BaseComponent {
 #element;
 #cvs;
 #ctx;
 #width = 480;
 #height = 320;
 #config = {
  colorpicker: false,
  width: '480px',
  height: '320px',
  format: 'stream',
  background: 'none'
 };
 #blank = false;
 #cursor = 'pen';
//  #color = '#FF0000';
 #color = '#000';
 #drawing = [];
 #isDrawing = false;
 #x = 0;
 #y = 0;

 events = new Events(this); 
 constructor(id, config = {}) {
  super();
  this.#element = this.setup(id, tpl, 'evodrawing', 'canvas', 'visual');
  const elem = this.getInitialElement();
  this.#config.background = this.getAttr(elem, 'background') == null ? 'none' : this.getAttr(elem, 'background');
  if (config.background !== undefined) this.#config.background = config.background;
  this.#config.format = this.getAttr(elem, 'format') == null ? 'stream' : this.getAttr(elem, 'format');
  if (config.format !== undefined) this.#config.format = config.format;
  this.#config.height = this.getAttr(elem, 'height') == null ? this.#height : this.getAttr(elem, 'height');
  if (config.height !== undefined) this.#config.height = config.height;
  this.#config.colorpicker = this.getAttr(elem, 'colorpicker') == null ? false : JSON.parse(this.getAttr(elem, 'colorpicker'));
  if (config.colorpicker !== undefined) this.#config.colorpicker = JSON.parse(config.colorpicker);
  if (this.#config.colorpicker == true) {
   const toolbars = this.#element.querySelector('div.toolbar');
   const a = document.createElement('a');
   a.setAttribute('class', 'btn btn-sm btn-primary fs--1');
   a.setAttribute('href', '#!btn-drawing');
   a.setAttribute('data-mode', 'colorpicker');
   a.setAttribute('role', 'button');
   a.innerHTML = '<i class="fa-solid fa-palette"></i>';
   toolbars.insertBefore(a, toolbars.firstChild);
   toolbars.firstChild.after(' ');
   const i = document.createElement('input');
   i.setAttribute('type', 'color');
   i.setAttribute('class', 'visually-hidden');
   toolbars.appendChild(i);
  }
  this.#config.width = this.getAttr(elem, 'width') == null ? this.#width : this.getAttr(elem, 'width');
  if (config.width !== undefined) this.#config.width = config.width;
  this.#element.style.width = this.#config.width;
  this.#element.style.height = this.#config.height;
  if (this.#config.background !== 'none' && this.#config.background !== '') this.#element.style.background = `url(${this.#config.background}) no-repeat`;
  this.#cvs = this.#element.querySelector('canvas');
  this.#ctx = this.#cvs.getContext("2d");
  this.#cvs.width = parseInt(this.#config.width);
  this.#cvs.height = parseInt(this.#config.height);
  if (config.field !== undefined) this.#cvs.setAttribute('data-field', config.field);
  this.#cvs.addEventListener('mousedown', e => this.#_handleStart(e));
  this.#cvs.addEventListener('mousemove', e => this.#_handleMove(e));
  this.#cvs.addEventListener('mouseup', e => this.#_handleStop(e));
  this.#cvs.addEventListener('touchstart', e => this.#_handleStart(e));
  this.#cvs.addEventListener('touchmove', e => this.#_handleMove(e));
  this.#cvs.addEventListener('touchend', e => this.#_handleStop(e));
  const changeColor = e => this.#color = e.target.value;
  const toolbar = this.#element.querySelectorAll('a[href="#!btn-drawing"');
  toolbar.forEach(t => {
   t.addEventListener('click', e => {
    e.preventDefault();
    const mode = e.target.dataset.mode;
    this.#cursor = mode;
    if (mode == 'colorpicker') {
     const i = this.#element.querySelector('input[type="color"]');
     i.click();
     i.removeEventListener('change', changeColor);
     i.addEventListener('change', changeColor); 
    };
    if (mode == 'trash') this.#_init();
    if (mode == 'upload') this.#_uploadfile();
   })
  })
  this.#_init();
 }
 #_uploadfile() {
  const elem = document.createElement('input');
  elem.setAttribute('type', 'file');
  elem.setAttribute('class', 'd-none');
  this.#element.appendChild(elem);
  const uploader = this.#element.querySelector('input[type="file"]');
  uploader.click();
  const reader = new FileReader();
  reader.onload = (e) => {
   const existing = this.drawing;
   this.drawing = e.target.result;
  };
  uploader.onchange = () => {
   const selectedFile = uploader.files[0];
   if (typeof selectedFile !== 'object') return false;
   reader.readAsDataURL(selectedFile);
  }
 }
 #_init() {
  this.#cvs = this.#element.querySelector('canvas');
  this.#ctx = this.#cvs.getContext("2d");
  this.#blank = true;
  this.#ctx.clearRect(0, 0, parseInt(this.#config.width), parseInt(this.#config.height));
  this.#drawing = [];
  this.#isDrawing = false;
  this.#cursor = 'pen';
 }
 #_drawLine = (ctx, x1, y1, x2, y2) => {
  let t = this;
  ctx.beginPath();
  ctx.strokeStyle = t.#color;
  ctx.lineWidth = 2;
  ctx.moveTo(x1, y1);
  ctx.lineTo(x2, y2);
  ctx.stroke();
  ctx.closePath();
 }
 #_getX(e) {
  let t = this;
  const onpopup = this.#element.parentElement.tagName.toLowerCase() == 'slot' ? 430 : 0;
  return (e.touches[0].pageX - t.#cvs.offsetLeft) - onpopup;
 }
 #_getY(e) {
  let t = this;
  const onpopup = this.#element.parentElement.tagName.toLowerCase() == 'slot' ? 80 : 0;
  return (e.touches[0].pageY - t.#cvs.offsetTop) - onpopup;
 }
 #_handleStart(e) {
  let t = this;
  t.#x = e.touches ? t.#_getX(e) : e.offsetX;
  t.#y = e.touches ? t.#_getY(e) : e.offsetY;
  t.#isDrawing = true;  
 }
 #_handleMove(e) {
  let t = this;
  if (!t.#isDrawing) return;
  if (t.#cursor == 'pen') {
   if (e.touches) {
    t.#_drawLine(t.#ctx, t.#x, t.#y, t.#_getX(e), t.#_getY(e));
    t.#drawing.push({x1: t.#x, y1: t.#y, x2: t.#_getX(e), y2: t.#_getY(e)});
    t.#x = t.#_getX(e);
    t.#y = t.#_getY(e);
   }else {
    t.#_drawLine(t.#ctx, t.#x, t.#y, e.offsetX, e.offsetY);
    t.#drawing.push({x1: t.#x, y1: t.#y, x2: e.offsetX, y2: e.offsetY});
    t.#x = e.offsetX;
    t.#y = e.offsetY;
   }
  }else if (t.#cursor == 'eraser') {
   t.#ctx.clearRect(t.#x, t.#y, 8, 8);
   if (e.touches) {
    t.#ctx.clearRect(t.#_getX(e), t.#_getY(e), 8, 8);
   }else {
    t.#ctx.clearRect(e.offsetX, e.offsetY, 8, 8);
   }
  } 
 }
 #_handleStop(e) {
  let t = this;
  if (!t.#isDrawing) return;
  if (e.touches) {
   t.#x = 0;
   t.#y = 0;
   t.#isDrawing = false;
   t.#blank = false;
  }else {
   if (t.#cursor == 'pen') {
    t.#_drawLine(t.#ctx, t.#x, t.#y, e.offsetX, e.offsetY);
    t.#drawing.push({x1: t.#x, y1: t.#y, x2: e.offsetX, y2: e.offsetY});
   }
   t.#x = 0;
   t.#y = 0;
   t.#isDrawing = false;
   t.#blank = false;
  }
 }
 get componentId() {
  return this.getComponentId();
 }
 set drawing(data) {
  let t = this;
  if (data == '') {
    this.#_init();
    return true;
  }
  if (this.#config.format == 'stream' || this.#config.format == 'all') {
   let tmpImage = new Image();
   tmpImage.onload = e => {
    this.#ctx.drawImage(tmpImage, 0, 0);
   }
   tmpImage.src = data;
  }else if (this.#config.format == 'coordinate') {
   data.forEach(coor => {
    t.#_drawLine(t.#ctx, coor.x1, coor.y1, coor.x2, coor.y2);
   })
  }  
  return true;
 }
 get drawing() {
  let t = this;
  let output = this.#cvs.toDataURL("image/png");
  if (this.#config.format == 'coordinate') {
   output = t.#blank == false ? t.#drawing : '';
  }
  if (this.#config.format == 'all') {
   output = {coord: t.#blank == false ? t.#drawing : '', stream: this.#cvs.toDataURL("image/png")};
  }
  return output;
 }
}