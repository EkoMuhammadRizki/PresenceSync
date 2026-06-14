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

/***/ "./resources/assets/core/js/custom/utilities/modals/create-project/files.js"
/*!**********************************************************************************!*\
  !*** ./resources/assets/core/js/custom/utilities/modals/create-project/files.js ***!
  \**********************************************************************************/
(module) {

eval("{\n\n// Class definition\nvar KTModalCreateProjectFiles = function () {\n  // Variables\n  var nextButton;\n  var previousButton;\n  var form;\n  var stepper;\n\n  // Private functions\n  var initForm = function initForm() {\n    // Project logo\n    // For more info about Dropzone plugin visit:  https://www.dropzonejs.com/#usage\n    var myDropzone = new Dropzone(\"#kt_modal_create_project_files_upload\", {\n      url: \"https://keenthemes.com/scripts/void.php\",\n      // Set the url for your upload script location\n      paramName: \"file\",\n      // The name that will be used to transfer the file\n      maxFiles: 10,\n      maxFilesize: 10,\n      // MB\n      addRemoveLinks: true,\n      accept: function accept(file, done) {\n        if (file.name == \"justinbieber.jpg\") {\n          done(\"Naha, you don't.\");\n        } else {\n          done();\n        }\n      }\n    });\n  };\n  var handleForm = function handleForm() {\n    nextButton.addEventListener('click', function (e) {\n      // Prevent default button action\n      e.preventDefault();\n\n      // Disable button to avoid multiple click \n      nextButton.disabled = true;\n\n      // Show loading indication\n      nextButton.setAttribute('data-kt-indicator', 'on');\n\n      // Simulate form submission\n      setTimeout(function () {\n        // Hide loading indication\n        nextButton.removeAttribute('data-kt-indicator');\n\n        // Enable button\n        nextButton.disabled = false;\n\n        // Go to next step\n        stepper.goNext();\n      }, 1500);\n    });\n    previousButton.addEventListener('click', function () {\n      stepper.goPrevious();\n    });\n  };\n  return {\n    // Public functions\n    init: function init() {\n      form = KTModalCreateProject.getForm();\n      stepper = KTModalCreateProject.getStepperObj();\n      nextButton = KTModalCreateProject.getStepper().querySelector('[data-kt-element=\"files-next\"]');\n      previousButton = KTModalCreateProject.getStepper().querySelector('[data-kt-element=\"files-previous\"]');\n      initForm();\n      handleForm();\n    }\n  };\n}();\n\n// Webpack support\nif ( true && typeof module.exports !== 'undefined') {\n  window.KTModalCreateProjectFiles = module.exports = KTModalCreateProjectFiles;\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9yZXNvdXJjZXMvYXNzZXRzL2NvcmUvanMvY3VzdG9tL3V0aWxpdGllcy9tb2RhbHMvY3JlYXRlLXByb2plY3QvZmlsZXMuanMiLCJtYXBwaW5ncyI6IkFBQWE7O0FBRWI7QUFDQSxJQUFJQSx5QkFBeUIsR0FBRyxZQUFZO0VBQzNDO0VBQ0EsSUFBSUMsVUFBVTtFQUNkLElBQUlDLGNBQWM7RUFDbEIsSUFBSUMsSUFBSTtFQUNSLElBQUlDLE9BQU87O0VBRVg7RUFDQSxJQUFJQyxRQUFRLEdBQUcsU0FBWEEsUUFBUUEsQ0FBQSxFQUFjO0lBQ3pCO0lBQ0E7SUFDQSxJQUFJQyxVQUFVLEdBQUcsSUFBSUMsUUFBUSxDQUFDLHVDQUF1QyxFQUFFO01BQ3RFQyxHQUFHLEVBQUUseUNBQXlDO01BQUU7TUFDdkNDLFNBQVMsRUFBRSxNQUFNO01BQUU7TUFDbkJDLFFBQVEsRUFBRSxFQUFFO01BQ1pDLFdBQVcsRUFBRSxFQUFFO01BQUU7TUFDakJDLGNBQWMsRUFBRSxJQUFJO01BQ3BCQyxNQUFNLEVBQUUsU0FBUkEsTUFBTUEsQ0FBV0MsSUFBSSxFQUFFQyxJQUFJLEVBQUU7UUFDekIsSUFBSUQsSUFBSSxDQUFDRSxJQUFJLElBQUksa0JBQWtCLEVBQUU7VUFDakNELElBQUksQ0FBQyxrQkFBa0IsQ0FBQztRQUM1QixDQUFDLE1BQU07VUFDSEEsSUFBSSxDQUFDLENBQUM7UUFDVjtNQUNKO0lBQ1YsQ0FBQyxDQUFDO0VBQ0gsQ0FBQztFQUVELElBQUlFLFVBQVUsR0FBRyxTQUFiQSxVQUFVQSxDQUFBLEVBQWM7SUFDM0JoQixVQUFVLENBQUNpQixnQkFBZ0IsQ0FBQyxPQUFPLEVBQUUsVUFBVUMsQ0FBQyxFQUFFO01BQ2pEO01BQ0FBLENBQUMsQ0FBQ0MsY0FBYyxDQUFDLENBQUM7O01BRWxCO01BQ0FuQixVQUFVLENBQUNvQixRQUFRLEdBQUcsSUFBSTs7TUFFMUI7TUFDQXBCLFVBQVUsQ0FBQ3FCLFlBQVksQ0FBQyxtQkFBbUIsRUFBRSxJQUFJLENBQUM7O01BRWxEO01BQ0FDLFVBQVUsQ0FBQyxZQUFXO1FBQ3JCO1FBQ0F0QixVQUFVLENBQUN1QixlQUFlLENBQUMsbUJBQW1CLENBQUM7O1FBRS9DO1FBQ0F2QixVQUFVLENBQUNvQixRQUFRLEdBQUcsS0FBSzs7UUFFM0I7UUFDQWpCLE9BQU8sQ0FBQ3FCLE1BQU0sQ0FBQyxDQUFDO01BQ2pCLENBQUMsRUFBRSxJQUFJLENBQUM7SUFDVCxDQUFDLENBQUM7SUFFRnZCLGNBQWMsQ0FBQ2dCLGdCQUFnQixDQUFDLE9BQU8sRUFBRSxZQUFZO01BQ3BEZCxPQUFPLENBQUNzQixVQUFVLENBQUMsQ0FBQztJQUNyQixDQUFDLENBQUM7RUFDSCxDQUFDO0VBRUQsT0FBTztJQUNOO0lBQ0FDLElBQUksRUFBRSxTQUFOQSxJQUFJQSxDQUFBLEVBQWM7TUFDakJ4QixJQUFJLEdBQUd5QixvQkFBb0IsQ0FBQ0MsT0FBTyxDQUFDLENBQUM7TUFDckN6QixPQUFPLEdBQUd3QixvQkFBb0IsQ0FBQ0UsYUFBYSxDQUFDLENBQUM7TUFDOUM3QixVQUFVLEdBQUcyQixvQkFBb0IsQ0FBQ0csVUFBVSxDQUFDLENBQUMsQ0FBQ0MsYUFBYSxDQUFDLGdDQUFnQyxDQUFDO01BQzlGOUIsY0FBYyxHQUFHMEIsb0JBQW9CLENBQUNHLFVBQVUsQ0FBQyxDQUFDLENBQUNDLGFBQWEsQ0FBQyxvQ0FBb0MsQ0FBQztNQUV0RzNCLFFBQVEsQ0FBQyxDQUFDO01BQ1ZZLFVBQVUsQ0FBQyxDQUFDO0lBQ2I7RUFDRCxDQUFDO0FBQ0YsQ0FBQyxDQUFDLENBQUM7O0FBRUg7QUFDQSxJQUFJLEtBQTZCLElBQUksT0FBT2dCLE1BQU0sQ0FBQ0MsT0FBTyxLQUFLLFdBQVcsRUFBRTtFQUMzRUMsTUFBTSxDQUFDbkMseUJBQXlCLEdBQUdpQyxNQUFNLENBQUNDLE9BQU8sR0FBR2xDLHlCQUF5QjtBQUM5RSIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3Jlc291cmNlcy9hc3NldHMvY29yZS9qcy9jdXN0b20vdXRpbGl0aWVzL21vZGFscy9jcmVhdGUtcHJvamVjdC9maWxlcy5qcz8xY2U4Il0sInNvdXJjZXNDb250ZW50IjpbIlwidXNlIHN0cmljdFwiO1xyXG5cclxuLy8gQ2xhc3MgZGVmaW5pdGlvblxyXG52YXIgS1RNb2RhbENyZWF0ZVByb2plY3RGaWxlcyA9IGZ1bmN0aW9uICgpIHtcclxuXHQvLyBWYXJpYWJsZXNcclxuXHR2YXIgbmV4dEJ1dHRvbjtcclxuXHR2YXIgcHJldmlvdXNCdXR0b247XHJcblx0dmFyIGZvcm07XHJcblx0dmFyIHN0ZXBwZXI7XHJcblxyXG5cdC8vIFByaXZhdGUgZnVuY3Rpb25zXHJcblx0dmFyIGluaXRGb3JtID0gZnVuY3Rpb24oKSB7XHJcblx0XHQvLyBQcm9qZWN0IGxvZ29cclxuXHRcdC8vIEZvciBtb3JlIGluZm8gYWJvdXQgRHJvcHpvbmUgcGx1Z2luIHZpc2l0OiAgaHR0cHM6Ly93d3cuZHJvcHpvbmVqcy5jb20vI3VzYWdlXHJcblx0XHR2YXIgbXlEcm9wem9uZSA9IG5ldyBEcm9wem9uZShcIiNrdF9tb2RhbF9jcmVhdGVfcHJvamVjdF9maWxlc191cGxvYWRcIiwgeyBcclxuXHRcdFx0dXJsOiBcImh0dHBzOi8va2VlbnRoZW1lcy5jb20vc2NyaXB0cy92b2lkLnBocFwiLCAvLyBTZXQgdGhlIHVybCBmb3IgeW91ciB1cGxvYWQgc2NyaXB0IGxvY2F0aW9uXHJcbiAgICAgICAgICAgIHBhcmFtTmFtZTogXCJmaWxlXCIsIC8vIFRoZSBuYW1lIHRoYXQgd2lsbCBiZSB1c2VkIHRvIHRyYW5zZmVyIHRoZSBmaWxlXHJcbiAgICAgICAgICAgIG1heEZpbGVzOiAxMCxcclxuICAgICAgICAgICAgbWF4RmlsZXNpemU6IDEwLCAvLyBNQlxyXG4gICAgICAgICAgICBhZGRSZW1vdmVMaW5rczogdHJ1ZSxcclxuICAgICAgICAgICAgYWNjZXB0OiBmdW5jdGlvbihmaWxlLCBkb25lKSB7XHJcbiAgICAgICAgICAgICAgICBpZiAoZmlsZS5uYW1lID09IFwianVzdGluYmllYmVyLmpwZ1wiKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgZG9uZShcIk5haGEsIHlvdSBkb24ndC5cIik7XHJcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgIGRvbmUoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG5cdFx0fSk7ICBcclxuXHR9XHJcblxyXG5cdHZhciBoYW5kbGVGb3JtID0gZnVuY3Rpb24oKSB7XHJcblx0XHRuZXh0QnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcclxuXHRcdFx0Ly8gUHJldmVudCBkZWZhdWx0IGJ1dHRvbiBhY3Rpb25cclxuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cclxuXHRcdFx0Ly8gRGlzYWJsZSBidXR0b24gdG8gYXZvaWQgbXVsdGlwbGUgY2xpY2sgXHJcblx0XHRcdG5leHRCdXR0b24uZGlzYWJsZWQgPSB0cnVlO1xyXG5cclxuXHRcdFx0Ly8gU2hvdyBsb2FkaW5nIGluZGljYXRpb25cclxuXHRcdFx0bmV4dEJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ2RhdGEta3QtaW5kaWNhdG9yJywgJ29uJyk7XHJcblxyXG5cdFx0XHQvLyBTaW11bGF0ZSBmb3JtIHN1Ym1pc3Npb25cclxuXHRcdFx0c2V0VGltZW91dChmdW5jdGlvbigpIHtcclxuXHRcdFx0XHQvLyBIaWRlIGxvYWRpbmcgaW5kaWNhdGlvblxyXG5cdFx0XHRcdG5leHRCdXR0b24ucmVtb3ZlQXR0cmlidXRlKCdkYXRhLWt0LWluZGljYXRvcicpO1xyXG5cclxuXHRcdFx0XHQvLyBFbmFibGUgYnV0dG9uXHJcblx0XHRcdFx0bmV4dEJ1dHRvbi5kaXNhYmxlZCA9IGZhbHNlO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIEdvIHRvIG5leHQgc3RlcFxyXG5cdFx0XHRcdHN0ZXBwZXIuZ29OZXh0KCk7XHJcblx0XHRcdH0sIDE1MDApOyBcdFx0XHJcblx0XHR9KTtcclxuXHJcblx0XHRwcmV2aW91c0J1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0c3RlcHBlci5nb1ByZXZpb3VzKCk7XHJcblx0XHR9KTtcclxuXHR9XHJcblxyXG5cdHJldHVybiB7XHJcblx0XHQvLyBQdWJsaWMgZnVuY3Rpb25zXHJcblx0XHRpbml0OiBmdW5jdGlvbiAoKSB7XHJcblx0XHRcdGZvcm0gPSBLVE1vZGFsQ3JlYXRlUHJvamVjdC5nZXRGb3JtKCk7XHJcblx0XHRcdHN0ZXBwZXIgPSBLVE1vZGFsQ3JlYXRlUHJvamVjdC5nZXRTdGVwcGVyT2JqKCk7XHJcblx0XHRcdG5leHRCdXR0b24gPSBLVE1vZGFsQ3JlYXRlUHJvamVjdC5nZXRTdGVwcGVyKCkucXVlcnlTZWxlY3RvcignW2RhdGEta3QtZWxlbWVudD1cImZpbGVzLW5leHRcIl0nKTtcclxuXHRcdFx0cHJldmlvdXNCdXR0b24gPSBLVE1vZGFsQ3JlYXRlUHJvamVjdC5nZXRTdGVwcGVyKCkucXVlcnlTZWxlY3RvcignW2RhdGEta3QtZWxlbWVudD1cImZpbGVzLXByZXZpb3VzXCJdJyk7XHJcblxyXG5cdFx0XHRpbml0Rm9ybSgpO1xyXG5cdFx0XHRoYW5kbGVGb3JtKCk7XHJcblx0XHR9XHJcblx0fTtcclxufSgpO1xyXG5cclxuLy8gV2VicGFjayBzdXBwb3J0XHJcbmlmICh0eXBlb2YgbW9kdWxlICE9PSAndW5kZWZpbmVkJyAmJiB0eXBlb2YgbW9kdWxlLmV4cG9ydHMgIT09ICd1bmRlZmluZWQnKSB7XHJcblx0d2luZG93LktUTW9kYWxDcmVhdGVQcm9qZWN0RmlsZXMgPSBtb2R1bGUuZXhwb3J0cyA9IEtUTW9kYWxDcmVhdGVQcm9qZWN0RmlsZXM7XHJcbn1cclxuIl0sIm5hbWVzIjpbIktUTW9kYWxDcmVhdGVQcm9qZWN0RmlsZXMiLCJuZXh0QnV0dG9uIiwicHJldmlvdXNCdXR0b24iLCJmb3JtIiwic3RlcHBlciIsImluaXRGb3JtIiwibXlEcm9wem9uZSIsIkRyb3B6b25lIiwidXJsIiwicGFyYW1OYW1lIiwibWF4RmlsZXMiLCJtYXhGaWxlc2l6ZSIsImFkZFJlbW92ZUxpbmtzIiwiYWNjZXB0IiwiZmlsZSIsImRvbmUiLCJuYW1lIiwiaGFuZGxlRm9ybSIsImFkZEV2ZW50TGlzdGVuZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJkaXNhYmxlZCIsInNldEF0dHJpYnV0ZSIsInNldFRpbWVvdXQiLCJyZW1vdmVBdHRyaWJ1dGUiLCJnb05leHQiLCJnb1ByZXZpb3VzIiwiaW5pdCIsIktUTW9kYWxDcmVhdGVQcm9qZWN0IiwiZ2V0Rm9ybSIsImdldFN0ZXBwZXJPYmoiLCJnZXRTdGVwcGVyIiwicXVlcnlTZWxlY3RvciIsIm1vZHVsZSIsImV4cG9ydHMiLCJ3aW5kb3ciXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/assets/core/js/custom/utilities/modals/create-project/files.js\n\n}");

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
/******/ 	var __webpack_exports__ = __webpack_require__("./resources/assets/core/js/custom/utilities/modals/create-project/files.js");
/******/ 	
/******/ })()
;