const numbers = [];
const events = [];
const getFormatedDate = (date, hari, tanggal, bulan, tahun, format = 'dd-mm-yyyy') => {
 let bulanString = '';
 let bulanStringShort = '';
 let jam = date.getHours();
 let menit = date.getMinutes();
 let detik = date.getSeconds();
 let result = `${hari}, ${tanggal}-${bulan}-${tahun}`;
 switch (hari) {
  case 0: hari = "Minggu"; break;
  case 1: hari = "Senin"; break;
  case 2: hari = "Selasa"; break;
  case 3: hari = "Rabu"; break;
  case 4: hari = "Kamis"; break;
  case 5: hari = "Jum'at"; break;
  case 6: hari = "Sabtu"; break;
 }
 tanggal = `0${tanggal}`.slice(-2);
 switch (bulan) {
  case 0: bulanString = "Januari"; break;
  case 1: bulanString = "Februari"; break;
  case 2: bulanString = "Maret"; break;
  case 3: bulanString = "April"; break;
  case 4: bulanString = "Mei"; break;
  case 5: bulanString = "Juni"; break;
  case 6: bulanString = "Juli"; break;
  case 7: bulanString = "Agustus"; break;
  case 8: bulanString = "September"; break;
  case 9: bulanString = "Oktober"; break;
  case 10: bulanString = "November"; break;
  case 11: bulanString = "Desember"; break;
 }
 switch (bulan) {
  case 0: bulanStringShort = "Jan"; break;
  case 1: bulanStringShort = "Feb"; break;
  case 2: bulanStringShort = "Mar"; break;
  case 3: bulanStringShort = "Apr"; break;
  case 4: bulanStringShort = "Mei"; break;
  case 5: bulanStringShort = "Jun"; break;
  case 6: bulanStringShort = "Jul"; break;
  case 7: bulanStringShort = "Agu"; break;
  case 8: bulanStringShort = "Sep"; break;
  case 9: bulanStringShort = "Okt"; break;
  case 10: bulanStringShort = "Nov"; break;
  case 11: bulanStringShort = "Des"; break;
 }
 bulan = `0${bulan}`.slice(-2);
 switch (format) {
  case 'yyyy': result = `${tahun}`; break;
  case 'mmm yyyy': result = `${bulanString} ${tahun}`; break;
  case 'dd-mm-yyyy': result = `${tanggal}-${bulan}-${tahun}`; break;
  case 'dd mmms yyyy': result = `${tanggal} ${bulanStringShort} ${tahun}`; break;
  case 'dd mmm yyyy': result = `${tanggal} ${bulanString} ${tahun}`; break;
  case 'hr, dd-mm-yyyy': result = `${hari}, ${tanggal}-${bulan}-${tahun}`; break;
  case 'dd-mm-yyyy hh:mm:ss': result = `${tanggal}-${bulan}-${tahun} ${jam}:${menit}:${detik}`; break;
  case 'hh:mm:ss': result = `${jam}:${menit}:${detik}`; break;
  case 'dd-mmm-yyyy': result = `${tanggal}-${bulanString}-${tahun}`; break;
  case 'dd-mmm-yyyy hh:mm:ss': result = `${tanggal}-${bulanString}-${tahun} ${jam}:${menit}:${detik}`; break;
  case 'hh:mm:ss': result = `${jam}:${menit}:${detik}`; break;
  case 'yyyy-mm-dd': result = `${tahun}-${bulan}-${tanggal}`; break;
  case 'yyyy-mm-dd hh:mm:ss': result = `${tahun}-${bulan}-${tanggal} ${jam}:${menit}:${detik}`; break;
 }
 return result;
}
export const getElem = (dataField, parent = null) => {
	const theNeedle = dataField.includes('[') ? dataField : `[data-field="${dataField}"]`;
	let theElement = document?.querySelector(theNeedle);
	if (parent !== null) {
		const parentElement = document?.querySelector(parent);
		theElement = parentElement?.querySelector(theNeedle);
	}
 return {
		get text() {
			return theElement.type.includes('select') ? theElement.options[theElement.selectedIndex].text : theElement.textContent;
		},
		get value() {
   return theElement?.value;
		},
		get instance() {
			return theElement;
		},
		set text(val) {
			if (theElement !== undefined && theElement !== null) theElement.textContent = val;
		},	
		set value(val) {
			if (theElement !== undefined && theElement !== null) theElement.value = val;
		}
	}
}
export const getElems = (dataField, parent = null) => {
	const theNeedle = dataField.includes('[') ? dataField : `[data-field="${dataField}"]`;
	let theElement = document?.querySelectorAll(theNeedle);
	if (parent !== null) {
		const parentElement = document?.querySelector(parent);
		theElement = parentElement?.querySelectorAll(theNeedle);
	}
 return theElement;
}
export const sleep = (ms) => {
 return new Promise(resolve => setTimeout(resolve, ms));
}
// export const setVisible = async(id, visible = true) => {
//  const elem = document.getElementById(id);
//  console.log(elem);
//  if (elem == undefined || elem == null) return false;
//  if (visible == false) elem.addClass('d-none');
//  if (visible == true) elem.removeClass('d-none');
// }
export const setVisible = async (id, visible = true) => {
  const elem = document.getElementById(id);
  if (elem == undefined || elem == null) return false;
  if (visible == false) elem.classList.add('d-none');
  if (visible == true) elem.classList.remove('d-none');
}
export const getBodyHeight = () => {
 let htmlElement = document.documentElement;
 let bodyElement = document.body;
 let height = htmlElement.clientHeight;
 return height;
}
export const getElementHeight = (elem) => {
 let element = document.querySelector(elem);
 return element.offsetHeight;
}
export const loadSession = (payload, key = '') => {
 let sessionData;
 let ss = sessionStorage.getItem(payload.id);
 if (ss !== null) {
  sessionData = environment !== 'production' && path === '/' ? JSON.parse(ss) : (config.debug ? JSON.parse(ss) : JSON.parse(_d(ss)));
  return sessionData == undefined ? {} : (key == '' ? sessionData : (sessionData[key] == undefined ? '' : sessionData[key]));
 }
 return false;
}
export const saveSession = (payload) => {
 if (environment !== 'production' && path === '/') {
  sessionStorage.setItem(payload.id, JSON.stringify(payload.data));
 } else {
  let dataset = config.debug ? JSON.stringify(payload.data) : _e(JSON.stringify(payload.data));
  sessionStorage.setItem(payload.id, dataset);
 }
 return true;
}
export const removeFromSession = (payload) => {
 sessionStorage.removeItem(payload);
 return true;
}
export const clearSession = () => {
 sessionStorage.clear();
 return true;
}
export const getCurrentUser = (prop = '') => {
 let usr = loadSession({id: 'app'});
 return prop == '' ? usr : usr[prop];
}
export const getSelectedPasien = (whichone, prop = '') => {
 let ps = loadSession({id: whichone});
 return prop == '' ? ps : ps[prop];
}
export const getFormEntries = (frm_entries, options = { dateAutoFormat: true, fromFormat: 'dd-mm-yyyy', toFormat: 'yyyy-mm-dd' }) => {
  let frmValues = {};
  let chk = {};
  const fields = frm_entries.querySelectorAll('[data-field]');
  fields.forEach(async (v) => {
    let tipe = v.dataset.type == undefined ? (v.type == undefined ? 'canvas' : v.type) : v.dataset.type;
    let id = v.dataset.field;
    let value = null;
    let svalue = null;
    if (tipe == 'rad' || tipe == 'radio') {
      if (v.checked) value = v.value;
    } else if (tipe == 'chk' || tipe == 'checkbox') {
      if (chk[id] == undefined) {
        let newObject = {};
        newObject[id] = [];
        chk = { ...chk, ...newObject };
      }
      if (v.checked) chk[id].push(v.value);
      value = chk[id].join(',');
    } else if (tipe == 'txt' || tipe == 'txtarea' || tipe == 'text' || tipe == 'textarea' || tipe == 'tel' || tipe == 'email' || tipe == 'number' || tipe == 'password' || tipe == 'url') {
      value = chrEncode(v.value);
    } else if (tipe == 'date' || tipe == 'time') {
      value = v.value;
      if (tipe == 'date' && options.dateAutoFormat) value = setFormat(v.value, { from: options.fromFormat, to: options.toFormat });
    } else if (tipe == 'sel' || tipe == 'select' || tipe == 'select-one') {
      value = chrEncode(v.value, ['"', ':']);
      if (v.dataset.value !== undefined) svalue = chrEncode(v.dataset.value, ['"', ':']);
    } else if (tipe == 'custom') {
      if (v.dataset.value == 'use-label') {
        let p = v.getAttribute('for');
        if (p !== undefined) {
          let pchecked = document.getElementById(p);
          if (pchecked.checked) value = v.innerHTML;
        } else {
          value = v.innerHTML;
        }
      } else {
        value = v.dataset.value;
      }
    }
    if (value !== null) {
      let newObject = {};
      newObject[id] = tipe == 'sel' || tipe == 'select' ? (svalue == null ? value : svalue) : value;
      frmValues = { ...frmValues, ...newObject };
    } else {
      let keys = Object.keys(frmValues);
      if (keys.includes(id) === false) {
        let newObject = {};
        newObject[id] = '';
        frmValues = { ...frmValues, ...newObject };
      }
    }
  })
  return frmValues;
}
export const setFormEntries = (frm_entries, objdata, allFields = true, options = { dateAutoFormat: true, fromFormat: 'yyyy-mm-dd', toFormat: 'dd-mm-yyyy' }) => {
  if (frm_entries === null) return false;
  const dataset = Object.keys(objdata).length == 1 ? objdata[0] : objdata;
  let fields = frm_entries.querySelectorAll('[data-field]');
  fields.forEach(async (v) => {
    let tipe = v.dataset.type == undefined ? (v.type == undefined ? 'canvas' : v.type) : v.dataset.type;
    if (tipe == 'rad' || tipe == 'radio') {
      v.checked = false;
    } else if (tipe == 'chk' || tipe == 'checkbox') {
      v.checked = false;
    } else if (tipe == 'txtarea' || tipe == 'textarea') {
      v.textContent = '';
      v.value = '';
    } else if (tipe == 'txt' || tipe == 'sel' || tipe == 'date' || tipe == 'time' || tipe == 'text' || tipe == 'select' || tipe == 'select-one') {
      v.value = '';
    } else if (tipe == 'custom') {
      if (v.dataset.value !== 'use-label') v.innerHTML = '';
    }
  })
  if (dataset.length == 0) return true;
  fields = frm_entries.querySelectorAll('[data-field]');
  fields.forEach(async (v) => {
    if (dataset[v.dataset.field] === undefined) {
      if (allFields) console.info(`kolom "${v.dataset.field}" tidak ditemukan di sumber data, kolom yang tersedia adalah`, Object.keys(dataset));
    } else {
      let tipe = v.dataset.type == undefined ? (v.type == undefined ? 'canvas' : v.type) : v.dataset.type;
      let id = v.dataset.field;
      if (tipe == 'rad' || tipe == 'radio') {
        v.checked = v.value == dataset[id];
      } else if (tipe == 'chk' || tipe == 'checkbox') {
        let values = dataset[id] === null ? '' : dataset[id];
        let isSubset = v.value.split(',').every(value => values.split(',').includes(value));
        v.checked = isSubset;
      } else if (tipe == 'txtarea' || tipe == 'textarea') {
        v.textContent = dataset[id];
        v.value = dataset[id];
      } else if (tipe == 'txt' || tipe == 'sel' || tipe == 'date' || tipe == 'time' || tipe == 'text' || tipe == 'select' || tipe == 'select-one') {
        v.value = dataset[id];
        if (v.dataset.value !== undefined) v.dataset.value = dataset[id];
        if (tipe == 'date' && options.dateAutoFormat) v.value = setFormat(dataset[id], { from: options.fromFormat, to: options.toFormat });
      } else if (tipe == 'select' || tipe == 'select-one') {  
        if (v.type !== v.dataset.type && options.componentInstances[id] !== undefined) options.componentInstances[id].value = dataset[id];
      } else if (tipe == 'custom') {
        if (v.dataset.value !== 'use-label') v.innerHTML = dataset[id];
      }
    }
  })
  return true;
}
export const calculateAge = (birth, format = 'yyyy-mm-dd') => {
 let splitter = '-';
 if (format.includes(':')) splitter = ':';
 if (format.includes('/')) splitter = '/';
 let splitted = birth.split(splitter); 
 let index = {d: 2, m: 1, y: 0};
 if (format.startsWith('mm')) index = {d: 1, m: 0, y: 2};
 if (format.startsWith('dd')) index = {d: 0, m: 1, y: 2}; 
 let formatedBirth = `${splitted[index.y]}${splitter}${splitted[index.m]}${splitter}${splitted[index.d]}`;
 let birthdate = new Date(formatedBirth);
 let today = new Date();
 if (birthdate > today) return {year: 0, month: 0, day: 0};
 let year_age = today.getFullYear() - birthdate.getFullYear();
 let month_age = (today.getMonth()+1) - (birthdate.getMonth()+1);
 let day_age = today.getDate() - birthdate.getDate();
 if (day_age < 0) {
  month_age = 0;
  let daysinbirthmonth = new Date(birthdate.getFullYear(), birthdate.getMonth()+1, 0).getDate();
  day_age = ((daysinbirthmonth - birthdate.getDate()) + today.getDate());
 }
 return {year: year_age, month: month_age, day: day_age};
}
export const ageGroup = (birthday) => {
 let kelompok_usia = 'neonatus';
 let inhari = (parseInt(birthday.umur_thn) * 365) + (parseInt(birthday.umur_bln) * 30) + parseInt(birthday.umur_hr);
 if (inhari > 28) kelompok_usia = 'anak';
 if (inhari > 6570) kelompok_usia = 'dewasa';
 return kelompok_usia;
}
export const calcDays = (date, days, ops = '+') => {
 const newDate = new Date(date);
 const cloneDate = new Date(date);
 if (ops == '+') newDate.setDate(cloneDate.getDate() + days);
 if (ops == '-') newDate.setDate(cloneDate.getDate() - days);
 return newDate;
}
export const formatTanggal = (date, format = 'hr, dd-mm-yyyy') => {
 const thedate = new Date(date);
 let tahun = thedate.getFullYear();
 let bulan = thedate.getMonth() + 1;
 let tanggal = thedate.getDate();
 let hari = thedate.getDay();
 return getFormatedDate(thedate, hari, tanggal, bulan, tahun, format);
}
export const tglSekarang = (format = 'hr, dd-mm-yyyy') => {
 let date = new Date();
 let tahun = date.getFullYear();
 let bulan = date.getMonth() + 1;
 let tanggal = date.getDate();
 let hari = date.getDay();
 return getFormatedDate(date, hari, tanggal, bulan, tahun, format);
}
export const setFormat = (data, options = {from: '', to: ''}) => {
 if (data == null || data == '') return '';
 let result = data;
 let splitterfrom = '-';
 let from = options.from;
 let to = options.to;
 if (from == '') return false;
 if (from.includes(':')) splitterfrom = ':';
 if (from.includes('/')) splitterfrom = '/';
 let splitterto = '-';
 if (to == '') return false;
 if (to.includes(':')) splitterto = ':';
 if (to.includes('/')) splitterto = '/';
 let mdate = data.toString();  
 let splitted = mdate.split(splitterfrom);
 let fromformat = from.split(splitterfrom).join('');
 let toformat = to.split(splitterto).join('');
 let index = {d: 0, m: 1, y: 2}; 
 if (fromformat == 'yyyymmdd') index = {d: 2, m: 1, y: 0};
 if (fromformat == 'ddmmyyyy') index = {d: 0, m: 1, y: 2};
 if (fromformat == 'mmddyyyy') index = {d: 1, m: 0, y: 2};
 if (toformat == 'yyyymmdd') result = `${splitted[index.y]}${splitterto}${splitted[index.m]}${splitterto}${splitted[index.d]}`;
 if (toformat == 'ddmmyyyy') result = `${splitted[index.d]}${splitterto}${splitted[index.m]}${splitterto}${splitted[index.y]}`;
 if (toformat == 'mmddyyyy') result = `${splitted[index.m]}${splitterto}${splitted[index.d]}${splitterto}${splitted[index.y]}`;
 return result;
}
export const chrEncode = (text, excludes = []) => {
 let tmp = text;
 // if (!excludes.includes('?')) tmp = tmp.replace(/\?/g, '%3F');
 if (!excludes.includes('"')) tmp = tmp.replace(/\"/g, '%22');   
 if (!excludes.includes('%')) tmp = tmp.replace(/\%/g, '%25');   
 if (!excludes.includes('&')) tmp = tmp.replace(/\&/g, '%26');   
 if (!excludes.includes('\'')) tmp = tmp.replace(/\'/g, '%27');   
 if (!excludes.includes('(')) tmp = tmp.replace(/\(/g, '%28');   
 if (!excludes.includes(')')) tmp = tmp.replace(/\)/g, '%29');   
 if (!excludes.includes('*')) tmp = tmp.replace(/\*/g, '%2A');   
 if (!excludes.includes('+')) tmp = tmp.replace(/\+/g, '%2B');   
 if (!excludes.includes(',')) tmp = tmp.replace(/\,/g, '%2C');   
 if (!excludes.includes('/')) tmp = tmp.replace(/\//g, '%2F');   
 if (!excludes.includes(':')) tmp = tmp.replace(/\:/g, '%3A');   
 if (!excludes.includes(';')) tmp = tmp.replace(/\;/g, '%3B');   
 if (!excludes.includes('=')) tmp = tmp.replace(/\=/g, '%3D');   
 if (!excludes.includes('@')) tmp = tmp.replace(/\@/g, '%40');   
 if (!excludes.includes('[')) tmp = tmp.replace(/\[/g, '%5B');   
 if (!excludes.includes(']')) tmp = tmp.replace(/\]/g, '%5D');
 return tmp;
}
export const chrDecode = (text) => {
 let tmp = text.replace(/\%20/g, "?");
 tmp = tmp.replace(/\%22/g, '"');   
 tmp = tmp.replace(/\%25/g, "%");   
 tmp = tmp.replace(/\%26/g, "&");   
 tmp = tmp.replace(/\%27/g, "'");   
 tmp = tmp.replace(/\%28/g, "(");   
 tmp = tmp.replace(/\%29/g, ")");   
 tmp = tmp.replace(/\%2A/g, "*");   
 tmp = tmp.replace(/\%2B/g, "+");   
 tmp = tmp.replace(/\%2C/g, ",");   
 tmp = tmp.replace(/\%2F/g, "/");   
 tmp = tmp.replace(/\%3A/g, ":");   
 tmp = tmp.replace(/\%3B/g, ";");   
 tmp = tmp.replace(/\%3D/g, "=");   
 tmp = tmp.replace(/\%3F/g, "?");   
 tmp = tmp.replace(/\%40/g, "@");   
 tmp = tmp.replace(/\%5B/g, "[");   
 tmp = tmp.replace(/\%5D/g, "]");
 return tmp;
}
export const formatText = (str) => {
 let arr = str.split(' ');
 let mapped = arr.map(s => s.at(0)+s.toLowerCase().substring(1));
 return mapped.join(arr.length > 1 ? ' ' : '');
}
export const nl2br = (str, is_xhtml) => {
  if (typeof str === 'undefined' || str === null) {
      return '';
  }
  let breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
  return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
export const formatCurrency = (number, localeID = 'id-ID') => {
 if (typeof number !== 'number') number = parseFloat(number);
 if (isNaN(number)) return 'Invalid number';
 return number.toLocaleString(localeID);
}
export const htmlElement = {
 show: (elem, cls = '') => elem.classList.remove(cls == '' ? 'd-none' : cls),
 hide: (elem, cls = '') => elem.classList.add(cls == '' ? 'd-none' : cls)
}
export const subscribe = (evName, fn) => {
 if (config.trace_info) console.info(`someone subscribe to ${evName}`);
 events[evName] = events[evName] || [];
 events[evName].push(fn);
}
export const unsubscribe = (evName, fn) => {
 if (evName == '*') {
  if (config.trace_info) console.info(`everyone unsubscribe from ${evName}`);
  events = [];
  return true;
 }
 if (events[evName]) {
  if (config.trace_info) console.info(`someone unsubscribe from ${evName}`);
  events[evName] = events[evName].filter(f => f !== fn);
 } 
}
export const publish = (evName, data) => {
 if (events[evName]) {
  if (config.trace_info) console.info(`someone publish ${evName}`);
  events[evName].forEach(f => {
   f(data);
  });
 } 
}
export const generateRandom = (min = 0, max = 100, unique = true) => {
 let num = Math.floor(Math.random() * (max - min + 1)) + min;
 if (unique) {
  if (numbers.includes(num) == false) {
   numbers.push(num);
  }else {
   num = -1;
  }
 }else {
  numbers.push(num);
 }
 return {num, numbers};
}

export const showDialog = (...args) => {
 if (typeof window !== 'undefined' && typeof window.showDialog === 'function') {
  return window.showDialog(...args);
 }
};

