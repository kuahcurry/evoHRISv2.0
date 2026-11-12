function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }
function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var camelize = function camelize(str) {
 var text = str.replace(/[-_\s.]+(.)?/g, function (_, c) {
   return c ? c.toUpperCase() : '';
 });
 return "".concat(text.substr(0, 1).toLowerCase()).concat(text.substr(1));
}; 
var getData = function getData(el, data) {
 try {
   return JSON.parse(el.dataset[camelize(data)]);
 } catch (e) {
   return el.dataset[camelize(data)];
 }
}; 
var hasClass = function hasClass(el, className) {
 !el && false;
 return el.classList.value.includes(className);
};
var addClass = function addClass(el, className) {
 el.classList.add(className);
};
var removeClass = function removeClass(el, className) {
 el.classList.remove(className);
};
var utils = {
 getData: getData,
 hasClass: hasClass,
 addClass: addClass,
 removeClass: removeClass
} 
var handleNavbarVerticalCollapsed = function handleNavbarVerticalCollapsed() {
  var html = document.documentElement;
  var navbarVerticalCollapse = document.getElementById('navbarVerticalCollapse');

  var toggleSidebar = function () {
    var isCurrentlyCollapsed = html.classList.contains('navbar-vertical-collapsed');
    if (isCurrentlyCollapsed) {
      html.classList.remove('navbar-vertical-collapsed');
      localStorage.setItem('isNavbarVerticalCollapsed', false);
    } else {
      html.classList.add('navbar-vertical-collapsed');
      localStorage.setItem('isNavbarVerticalCollapsed', true);
    }
    html.classList.remove('navbar-vertical-collapsed-hover');
    sessionStorage.setItem('sidebar_manually_toggled', 'true');

    window.dispatchEvent(new Event('resize'));
    window.dispatchEvent(new CustomEvent('navbar.vertical.toggle'));
    clearTimeout(window.__sidebarResizeTimer);
    window.__sidebarResizeTimer = setTimeout(function () {
      window.dispatchEvent(new Event('resize'));
    }, 230);
  };

  var toggleButtons = document.querySelectorAll('.navbar-vertical-toggle, .navbar-top .navbar-toggler');
  toggleButtons.forEach(function (btn) {
    if (!btn._hasSidebarToggle) {
      btn._hasSidebarToggle = true;
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
      });
    }
  });

  // Pastikan tidak ada class hover yang tertinggal agar sidebar tetap diam di posisinya (tidak membuka saat kursor lewat)
  if (navbarVerticalCollapse) {
    html.classList.remove('navbar-vertical-collapsed-hover');
  }
};
var togglePaginationButtonDisable = function togglePaginationButtonDisable(button, disabled) {
 button.disabled = disabled;
 button.classList[disabled ? 'add' : 'remove']('disabled');
};
var listInit = function listInit() {
 if (window.List) {
   var lists = document.querySelectorAll('[data-list]');
   if (lists.length) {
     lists.forEach(function (el) {
       var bulkSelect = el.querySelector('[data-bulk-select]');
       var options = utils.getData(el, 'list');
       if (options.pagination) {
         options = _objectSpread(_objectSpread({}, options), {}, {
           pagination: _objectSpread({
             item: "<li><button class='page' type='button'></button></li>"
           }, options.pagination)
         });
       }
       var paginationButtonNext = el.querySelector('[data-list-pagination="next"]');
       var paginationButtonPrev = el.querySelector('[data-list-pagination="prev"]');
       var viewAll = el.querySelector('[data-list-view="*"]');
       var viewLess = el.querySelector('[data-list-view="less"]');
       var listInfo = el.querySelector('[data-list-info]');
       var listFilter = document.querySelector('[data-list-filter]');
       var list = new window.List(el, options);

       //-------fallback-----------

       list.on('updated', function (item) {
         var fallback = el.querySelector('.fallback') || document.getElementById(options.fallback);
         if (fallback) {
           if (item.matchingItems.length === 0) {
             fallback.classList.remove('d-none');
           } else {
             fallback.classList.add('d-none');
           }
         }
       });

       // ---------------------------------------

       var totalItem = list.items.length;
       var itemsPerPage = list.page;
       var btnDropdownClose = list.listContainer.querySelector('.btn-close');
       var pageQuantity = Math.ceil(totalItem / itemsPerPage);
       var numberOfcurrentItems = list.visibleItems.length;
       var pageCount = 1;
       btnDropdownClose && btnDropdownClose.addEventListener('search.close', function () {
         list.fuzzySearch('');
       });
       var updateListControls = function updateListControls() {
         listInfo && (listInfo.innerHTML = "".concat(list.i, " to ").concat(numberOfcurrentItems, " of ").concat(totalItem));
         paginationButtonPrev && togglePaginationButtonDisable(paginationButtonPrev, pageCount === 1);
         paginationButtonNext && togglePaginationButtonDisable(paginationButtonNext, pageCount === pageQuantity);
         if (pageCount > 1 && pageCount < pageQuantity) {
           togglePaginationButtonDisable(paginationButtonNext, false);
           togglePaginationButtonDisable(paginationButtonPrev, false);
         }
       };

       // List info
       updateListControls();
       if (paginationButtonNext) {
         paginationButtonNext.addEventListener('click', function (e) {
           e.preventDefault();
           pageCount += 1;
           var nextInitialIndex = list.i + itemsPerPage;
           nextInitialIndex <= list.size() && list.show(nextInitialIndex, itemsPerPage);
           numberOfcurrentItems += list.visibleItems.length;
           updateListControls();
         });
       }
       if (paginationButtonPrev) {
         paginationButtonPrev.addEventListener('click', function (e) {
           e.preventDefault();
           pageCount -= 1;
           numberOfcurrentItems -= list.visibleItems.length;
           var prevItem = list.i - itemsPerPage;
           prevItem > 0 && list.show(prevItem, itemsPerPage);
           updateListControls();
         });
       }
       var toggleViewBtn = function toggleViewBtn() {
         viewLess.classList.toggle('d-none');
         viewAll.classList.toggle('d-none');
       };
       if (viewAll) {
         viewAll.addEventListener('click', function () {
           list.show(1, totalItem);
           pageQuantity = 1;
           pageCount = 1;
           numberOfcurrentItems = totalItem;
           updateListControls();
           toggleViewBtn();
         });
       }
       if (viewLess) {
         viewLess.addEventListener('click', function () {
           list.show(1, itemsPerPage);
           pageQuantity = Math.ceil(totalItem / itemsPerPage);
           pageCount = 1;
           numberOfcurrentItems = list.visibleItems.length;
           updateListControls();
           toggleViewBtn();
         });
       }
       // numbering pagination
       if (options.pagination) {
         el.querySelector('.pagination').addEventListener('click', function (e) {
           if (e.target.classList[0] === 'page') {
             pageCount = Number(e.target.innerText);
             updateListControls();
           }
         });
       }
       if (options.filter) {
         var key = options.filter.key;
         listFilter.addEventListener('change', function (e) {
           list.filter(function (item) {
             if (e.target.value === '') {
               return true;
             }
             return item.values()[key].toLowerCase().includes(e.target.value.toLowerCase());
           });
         });
       }

       //bulk-select
       if (bulkSelect) {
         var bulkSelectInstance = window.BulkSelect.getInstance(bulkSelect);
         bulkSelectInstance.attachRowNodes(list.items.map(function (item) {
           return item.elm.querySelector('[data-bulk-select-row]');
         }));
         bulkSelect.addEventListener('change', function () {
           if (list) {
             if (bulkSelect.checked) {
               list.items.forEach(function (item) {
                 item.elm.querySelector('[data-bulk-select-row]').checked = true;
               });
             } else {
               list.items.forEach(function (item) {
                 item.elm.querySelector('[data-bulk-select-row]').checked = false;
               });
             }
           }
         });
       }
     });
   }
 }
};