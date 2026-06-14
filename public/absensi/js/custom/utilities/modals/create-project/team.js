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

/***/ "./resources/assets/core/js/custom/utilities/modals/create-project/team.js"
/*!*********************************************************************************!*\
  !*** ./resources/assets/core/js/custom/utilities/modals/create-project/team.js ***!
  \*********************************************************************************/
(module) {

eval("{\n\n// Class definition\nvar KTModalCreateProjectTeam = function () {\n  // Variables\n  var nextButton;\n  var previousButton;\n  var form;\n  var stepper;\n\n  // Private functions\n  var handleForm = function handleForm() {\n    nextButton.addEventListener('click', function (e) {\n      // Prevent default button action\n      e.preventDefault();\n\n      // Disable button to avoid multiple click \n      nextButton.disabled = true;\n\n      // Show loading indication\n      nextButton.setAttribute('data-kt-indicator', 'on');\n\n      // Simulate form submission\n      setTimeout(function () {\n        // Enable button\n        nextButton.disabled = false;\n\n        // Simulate form submission\n        nextButton.removeAttribute('data-kt-indicator');\n\n        // Go to next step\n        stepper.goNext();\n      }, 1500);\n    });\n    previousButton.addEventListener('click', function () {\n      stepper.goPrevious();\n    });\n  };\n  return {\n    // Public functions\n    init: function init() {\n      form = KTModalCreateProject.getForm();\n      stepper = KTModalCreateProject.getStepperObj();\n      nextButton = KTModalCreateProject.getStepper().querySelector('[data-kt-element=\"team-next\"]');\n      previousButton = KTModalCreateProject.getStepper().querySelector('[data-kt-element=\"team-previous\"]');\n      handleForm();\n    }\n  };\n}();\n\n// Webpack support\nif ( true && typeof module.exports !== 'undefined') {\n  window.KTModalCreateProjectTeam = module.exports = KTModalCreateProjectTeam;\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9yZXNvdXJjZXMvYXNzZXRzL2NvcmUvanMvY3VzdG9tL3V0aWxpdGllcy9tb2RhbHMvY3JlYXRlLXByb2plY3QvdGVhbS5qcyIsIm1hcHBpbmdzIjoiQUFBYTs7QUFFYjtBQUNBLElBQUlBLHdCQUF3QixHQUFHLFlBQVk7RUFDMUM7RUFDQSxJQUFJQyxVQUFVO0VBQ2QsSUFBSUMsY0FBYztFQUNsQixJQUFJQyxJQUFJO0VBQ1IsSUFBSUMsT0FBTzs7RUFFWDtFQUNBLElBQUlDLFVBQVUsR0FBRyxTQUFiQSxVQUFVQSxDQUFBLEVBQWM7SUFDM0JKLFVBQVUsQ0FBQ0ssZ0JBQWdCLENBQUMsT0FBTyxFQUFFLFVBQVVDLENBQUMsRUFBRTtNQUNqRDtNQUNBQSxDQUFDLENBQUNDLGNBQWMsQ0FBQyxDQUFDOztNQUVsQjtNQUNBUCxVQUFVLENBQUNRLFFBQVEsR0FBRyxJQUFJOztNQUUxQjtNQUNBUixVQUFVLENBQUNTLFlBQVksQ0FBQyxtQkFBbUIsRUFBRSxJQUFJLENBQUM7O01BRWxEO01BQ0FDLFVBQVUsQ0FBQyxZQUFXO1FBQ3JCO1FBQ0FWLFVBQVUsQ0FBQ1EsUUFBUSxHQUFHLEtBQUs7O1FBRTNCO1FBQ0FSLFVBQVUsQ0FBQ1csZUFBZSxDQUFDLG1CQUFtQixDQUFDOztRQUUvQztRQUNBUixPQUFPLENBQUNTLE1BQU0sQ0FBQyxDQUFDO01BQ2pCLENBQUMsRUFBRSxJQUFJLENBQUM7SUFDVCxDQUFDLENBQUM7SUFFRlgsY0FBYyxDQUFDSSxnQkFBZ0IsQ0FBQyxPQUFPLEVBQUUsWUFBWTtNQUNwREYsT0FBTyxDQUFDVSxVQUFVLENBQUMsQ0FBQztJQUNyQixDQUFDLENBQUM7RUFDSCxDQUFDO0VBRUQsT0FBTztJQUNOO0lBQ0FDLElBQUksRUFBRSxTQUFOQSxJQUFJQSxDQUFBLEVBQWM7TUFDakJaLElBQUksR0FBR2Esb0JBQW9CLENBQUNDLE9BQU8sQ0FBQyxDQUFDO01BQ3JDYixPQUFPLEdBQUdZLG9CQUFvQixDQUFDRSxhQUFhLENBQUMsQ0FBQztNQUM5Q2pCLFVBQVUsR0FBR2Usb0JBQW9CLENBQUNHLFVBQVUsQ0FBQyxDQUFDLENBQUNDLGFBQWEsQ0FBQywrQkFBK0IsQ0FBQztNQUM3RmxCLGNBQWMsR0FBR2Msb0JBQW9CLENBQUNHLFVBQVUsQ0FBQyxDQUFDLENBQUNDLGFBQWEsQ0FBQyxtQ0FBbUMsQ0FBQztNQUVyR2YsVUFBVSxDQUFDLENBQUM7SUFDYjtFQUNELENBQUM7QUFDRixDQUFDLENBQUMsQ0FBQzs7QUFFSDtBQUNBLElBQUksS0FBNkIsSUFBSSxPQUFPZ0IsTUFBTSxDQUFDQyxPQUFPLEtBQUssV0FBVyxFQUFFO0VBQzNFQyxNQUFNLENBQUN2Qix3QkFBd0IsR0FBR3FCLE1BQU0sQ0FBQ0MsT0FBTyxHQUFHdEIsd0JBQXdCO0FBQzVFIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vcmVzb3VyY2VzL2Fzc2V0cy9jb3JlL2pzL2N1c3RvbS91dGlsaXRpZXMvbW9kYWxzL2NyZWF0ZS1wcm9qZWN0L3RlYW0uanM/NDdjOCJdLCJzb3VyY2VzQ29udGVudCI6WyJcInVzZSBzdHJpY3RcIjtcclxuXHJcbi8vIENsYXNzIGRlZmluaXRpb25cclxudmFyIEtUTW9kYWxDcmVhdGVQcm9qZWN0VGVhbSA9IGZ1bmN0aW9uICgpIHtcclxuXHQvLyBWYXJpYWJsZXNcclxuXHR2YXIgbmV4dEJ1dHRvbjtcclxuXHR2YXIgcHJldmlvdXNCdXR0b247XHJcblx0dmFyIGZvcm07XHJcblx0dmFyIHN0ZXBwZXI7XHJcblxyXG5cdC8vIFByaXZhdGUgZnVuY3Rpb25zXHJcblx0dmFyIGhhbmRsZUZvcm0gPSBmdW5jdGlvbigpIHtcclxuXHRcdG5leHRCdXR0b24uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBmdW5jdGlvbiAoZSkge1xyXG5cdFx0XHQvLyBQcmV2ZW50IGRlZmF1bHQgYnV0dG9uIGFjdGlvblxyXG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblxyXG5cdFx0XHQvLyBEaXNhYmxlIGJ1dHRvbiB0byBhdm9pZCBtdWx0aXBsZSBjbGljayBcclxuXHRcdFx0bmV4dEJ1dHRvbi5kaXNhYmxlZCA9IHRydWU7XHJcblxyXG5cdFx0XHQvLyBTaG93IGxvYWRpbmcgaW5kaWNhdGlvblxyXG5cdFx0XHRuZXh0QnV0dG9uLnNldEF0dHJpYnV0ZSgnZGF0YS1rdC1pbmRpY2F0b3InLCAnb24nKTtcclxuXHJcblx0XHRcdC8vIFNpbXVsYXRlIGZvcm0gc3VibWlzc2lvblxyXG5cdFx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdC8vIEVuYWJsZSBidXR0b25cclxuXHRcdFx0XHRuZXh0QnV0dG9uLmRpc2FibGVkID0gZmFsc2U7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gU2ltdWxhdGUgZm9ybSBzdWJtaXNzaW9uXHJcblx0XHRcdFx0bmV4dEJ1dHRvbi5yZW1vdmVBdHRyaWJ1dGUoJ2RhdGEta3QtaW5kaWNhdG9yJyk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gR28gdG8gbmV4dCBzdGVwXHJcblx0XHRcdFx0c3RlcHBlci5nb05leHQoKTtcclxuXHRcdFx0fSwgMTUwMCk7IFx0XHRcclxuXHRcdH0pO1xyXG5cclxuXHRcdHByZXZpb3VzQnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRzdGVwcGVyLmdvUHJldmlvdXMoKTtcclxuXHRcdH0pO1xyXG5cdH1cclxuXHJcblx0cmV0dXJuIHtcclxuXHRcdC8vIFB1YmxpYyBmdW5jdGlvbnNcclxuXHRcdGluaXQ6IGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0Zm9ybSA9IEtUTW9kYWxDcmVhdGVQcm9qZWN0LmdldEZvcm0oKTtcclxuXHRcdFx0c3RlcHBlciA9IEtUTW9kYWxDcmVhdGVQcm9qZWN0LmdldFN0ZXBwZXJPYmooKTtcclxuXHRcdFx0bmV4dEJ1dHRvbiA9IEtUTW9kYWxDcmVhdGVQcm9qZWN0LmdldFN0ZXBwZXIoKS5xdWVyeVNlbGVjdG9yKCdbZGF0YS1rdC1lbGVtZW50PVwidGVhbS1uZXh0XCJdJyk7XHJcblx0XHRcdHByZXZpb3VzQnV0dG9uID0gS1RNb2RhbENyZWF0ZVByb2plY3QuZ2V0U3RlcHBlcigpLnF1ZXJ5U2VsZWN0b3IoJ1tkYXRhLWt0LWVsZW1lbnQ9XCJ0ZWFtLXByZXZpb3VzXCJdJyk7XHJcblxyXG5cdFx0XHRoYW5kbGVGb3JtKCk7XHJcblx0XHR9XHJcblx0fTtcclxufSgpO1xyXG5cclxuLy8gV2VicGFjayBzdXBwb3J0XHJcbmlmICh0eXBlb2YgbW9kdWxlICE9PSAndW5kZWZpbmVkJyAmJiB0eXBlb2YgbW9kdWxlLmV4cG9ydHMgIT09ICd1bmRlZmluZWQnKSB7XHJcblx0d2luZG93LktUTW9kYWxDcmVhdGVQcm9qZWN0VGVhbSA9IG1vZHVsZS5leHBvcnRzID0gS1RNb2RhbENyZWF0ZVByb2plY3RUZWFtO1xyXG59Il0sIm5hbWVzIjpbIktUTW9kYWxDcmVhdGVQcm9qZWN0VGVhbSIsIm5leHRCdXR0b24iLCJwcmV2aW91c0J1dHRvbiIsImZvcm0iLCJzdGVwcGVyIiwiaGFuZGxlRm9ybSIsImFkZEV2ZW50TGlzdGVuZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJkaXNhYmxlZCIsInNldEF0dHJpYnV0ZSIsInNldFRpbWVvdXQiLCJyZW1vdmVBdHRyaWJ1dGUiLCJnb05leHQiLCJnb1ByZXZpb3VzIiwiaW5pdCIsIktUTW9kYWxDcmVhdGVQcm9qZWN0IiwiZ2V0Rm9ybSIsImdldFN0ZXBwZXJPYmoiLCJnZXRTdGVwcGVyIiwicXVlcnlTZWxlY3RvciIsIm1vZHVsZSIsImV4cG9ydHMiLCJ3aW5kb3ciXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/assets/core/js/custom/utilities/modals/create-project/team.js\n\n}");

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module is referenced by other modules so it can't be inlined
/******/ 	var __webpack_exports__ = __webpack_require__("./resources/assets/core/js/custom/utilities/modals/create-project/team.js");
/******/ 	
/******/ })()
;