/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/assets/core/js/custom/utilities/search/horizontal.js"
/*!************************************************************************!*\
  !*** ./resources/assets/core/js/custom/utilities/search/horizontal.js ***!
  \************************************************************************/
() {

eval("{\n\n// Class definition\nvar KTSearchHorizontal = function () {\n  // Private functions\n  var initAdvancedSearchForm = function initAdvancedSearchForm() {\n    var form = document.querySelector('#kt_advanced_search_form');\n\n    // Init tags\n    var tags = form.querySelector('[name=\"tags\"]');\n    new Tagify(tags);\n  };\n  var handleAdvancedSearchToggle = function handleAdvancedSearchToggle() {\n    var link = document.querySelector('#kt_horizontal_search_advanced_link');\n    link.addEventListener('click', function (e) {\n      e.preventDefault();\n      if (link.innerHTML === \"Advanced Search\") {\n        link.innerHTML = \"Hide Advanced Search\";\n      } else {\n        link.innerHTML = \"Advanced Search\";\n      }\n    });\n  };\n\n  // Public methods\n  return {\n    init: function init() {\n      initAdvancedSearchForm();\n      handleAdvancedSearchToggle();\n    }\n  };\n}();\n\n// On document ready\nKTUtil.onDOMContentLoaded(function () {\n  KTSearchHorizontal.init();\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9yZXNvdXJjZXMvYXNzZXRzL2NvcmUvanMvY3VzdG9tL3V0aWxpdGllcy9zZWFyY2gvaG9yaXpvbnRhbC5qcyIsIm1hcHBpbmdzIjoiQUFBYTs7QUFFYjtBQUNBLElBQUlBLGtCQUFrQixHQUFHLFlBQVk7RUFDakM7RUFDQSxJQUFJQyxzQkFBc0IsR0FBRyxTQUF6QkEsc0JBQXNCQSxDQUFBLEVBQWU7SUFDdEMsSUFBSUMsSUFBSSxHQUFHQyxRQUFRLENBQUNDLGFBQWEsQ0FBQywwQkFBMEIsQ0FBQzs7SUFFN0Q7SUFDQSxJQUFJQyxJQUFJLEdBQUdILElBQUksQ0FBQ0UsYUFBYSxDQUFDLGVBQWUsQ0FBQztJQUM5QyxJQUFJRSxNQUFNLENBQUNELElBQUksQ0FBQztFQUNuQixDQUFDO0VBRUQsSUFBSUUsMEJBQTBCLEdBQUcsU0FBN0JBLDBCQUEwQkEsQ0FBQSxFQUFlO0lBQ3pDLElBQUlDLElBQUksR0FBR0wsUUFBUSxDQUFDQyxhQUFhLENBQUMscUNBQXFDLENBQUM7SUFFeEVJLElBQUksQ0FBQ0MsZ0JBQWdCLENBQUMsT0FBTyxFQUFFLFVBQVVDLENBQUMsRUFBRTtNQUN4Q0EsQ0FBQyxDQUFDQyxjQUFjLENBQUMsQ0FBQztNQUVsQixJQUFJSCxJQUFJLENBQUNJLFNBQVMsS0FBSyxpQkFBaUIsRUFBRTtRQUN0Q0osSUFBSSxDQUFDSSxTQUFTLEdBQUcsc0JBQXNCO01BQzNDLENBQUMsTUFBTTtRQUNISixJQUFJLENBQUNJLFNBQVMsR0FBRyxpQkFBaUI7TUFDdEM7SUFDSixDQUFDLENBQUM7RUFDTixDQUFDOztFQUVEO0VBQ0EsT0FBTztJQUNIQyxJQUFJLEVBQUUsU0FBTkEsSUFBSUEsQ0FBQSxFQUFjO01BQ2RaLHNCQUFzQixDQUFDLENBQUM7TUFDeEJNLDBCQUEwQixDQUFDLENBQUM7SUFDaEM7RUFDSixDQUFDO0FBQ0wsQ0FBQyxDQUFDLENBQUM7O0FBRUg7QUFDQU8sTUFBTSxDQUFDQyxrQkFBa0IsQ0FBQyxZQUFZO0VBQ2xDZixrQkFBa0IsQ0FBQ2EsSUFBSSxDQUFDLENBQUM7QUFDN0IsQ0FBQyxDQUFDIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vcmVzb3VyY2VzL2Fzc2V0cy9jb3JlL2pzL2N1c3RvbS91dGlsaXRpZXMvc2VhcmNoL2hvcml6b250YWwuanM/YjhjNiJdLCJzb3VyY2VzQ29udGVudCI6WyJcInVzZSBzdHJpY3RcIjtcclxuIFxyXG4vLyBDbGFzcyBkZWZpbml0aW9uXHJcbnZhciBLVFNlYXJjaEhvcml6b250YWwgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAvLyBQcml2YXRlIGZ1bmN0aW9uc1xyXG4gICAgdmFyIGluaXRBZHZhbmNlZFNlYXJjaEZvcm0gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICB2YXIgZm9ybSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNrdF9hZHZhbmNlZF9zZWFyY2hfZm9ybScpO1xyXG5cclxuICAgICAgIC8vIEluaXQgdGFnc1xyXG4gICAgICAgdmFyIHRhZ3MgPSBmb3JtLnF1ZXJ5U2VsZWN0b3IoJ1tuYW1lPVwidGFnc1wiXScpO1xyXG4gICAgICAgbmV3IFRhZ2lmeSh0YWdzKTtcclxuICAgIH1cclxuXHJcbiAgICB2YXIgaGFuZGxlQWR2YW5jZWRTZWFyY2hUb2dnbGUgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIGxpbmsgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcja3RfaG9yaXpvbnRhbF9zZWFyY2hfYWR2YW5jZWRfbGluaycpO1xyXG5cclxuICAgICAgICBsaW5rLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgICAgICBcclxuICAgICAgICAgICAgaWYgKGxpbmsuaW5uZXJIVE1MID09PSBcIkFkdmFuY2VkIFNlYXJjaFwiKSB7XHJcbiAgICAgICAgICAgICAgICBsaW5rLmlubmVySFRNTCA9IFwiSGlkZSBBZHZhbmNlZCBTZWFyY2hcIjtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIGxpbmsuaW5uZXJIVE1MID0gXCJBZHZhbmNlZCBTZWFyY2hcIjtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pXHJcbiAgICB9XHJcblxyXG4gICAgLy8gUHVibGljIG1ldGhvZHNcclxuICAgIHJldHVybiB7XHJcbiAgICAgICAgaW5pdDogZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBpbml0QWR2YW5jZWRTZWFyY2hGb3JtKCk7XHJcbiAgICAgICAgICAgIGhhbmRsZUFkdmFuY2VkU2VhcmNoVG9nZ2xlKCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSAgICAgXHJcbn0oKTtcclxuXHJcbi8vIE9uIGRvY3VtZW50IHJlYWR5XHJcbktUVXRpbC5vbkRPTUNvbnRlbnRMb2FkZWQoZnVuY3Rpb24gKCkge1xyXG4gICAgS1RTZWFyY2hIb3Jpem9udGFsLmluaXQoKTtcclxufSk7XHJcbiJdLCJuYW1lcyI6WyJLVFNlYXJjaEhvcml6b250YWwiLCJpbml0QWR2YW5jZWRTZWFyY2hGb3JtIiwiZm9ybSIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsInRhZ3MiLCJUYWdpZnkiLCJoYW5kbGVBZHZhbmNlZFNlYXJjaFRvZ2dsZSIsImxpbmsiLCJhZGRFdmVudExpc3RlbmVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwiaW5uZXJIVE1MIiwiaW5pdCIsIktUVXRpbCIsIm9uRE9NQ29udGVudExvYWRlZCJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/assets/core/js/custom/utilities/search/horizontal.js\n\n}");

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./resources/assets/core/js/custom/utilities/search/horizontal.js"]();
/******/ 	
/******/ })()
;