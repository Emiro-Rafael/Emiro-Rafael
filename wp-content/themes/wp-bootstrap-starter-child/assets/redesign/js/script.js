const only_num = /^[0-9.]+$/;
const only_num_replace = /[^0-9.]/g;
const email_reg = /^(([^<>()\[\]\\.,;:\s@"]{1,62}(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z-аА-яЯ\-0-9]+\.)+[a-zA-Z-аА-яЯ]{2,62}))$/;
const INACTIVITY_LIMIT = 5 * 60 * 1000;
const validationRules = {
  'email': {
    'rules': {
      regex: email_reg
    }
  },
  'numeric': {
    'rules': {
      regex: only_num
    }
  },
  'password': {
    'rules': {
      password: true
    }
  },
  'password_repeat': {
    'rules': {
      password_repeat: true
    }
  },
  'email_or_customer': {
    'rules': {
      email_or_customer: [email_reg, /^cus/]
    }
  }
}

if(typeof hasPluginRequirements === 'undefined') {
  function hasPluginRequirements(pluginName, requirements) {
    let noRequiredFunctions = []

    requirements.forEach(function (requirement) {
      if (typeof this[requirement] !== 'function') {
        noRequiredFunctions.push(requirement)
      }
    })

    if (noRequiredFunctions.length) {
      console.error(`BLACKBOOK plugin "${pluginName}": requires ${noRequiredFunctions.join('(), ')}() functions`)
    }
    return !noRequiredFunctions.length
  }
}
if(typeof selectAll === 'undefined') {
  function selectAll(selector, container = false) {
    return Array.from(!container ? document.querySelectorAll(selector) : container.querySelectorAll(selector));
  }
}
if(typeof printf === 'undefined') {
  function printf(string, vars = [], addToEnd = true, char = '&') {
    vars.forEach(function (thisVar, index) {
      let r = new RegExp(char + (index + 1) + '(?![0-9])', 'g');

      if(r.test(string)) {
        string = string.replace(r, thisVar);
      } else if(addToEnd) {
        string += ' '+thisVar
      }
    })
    return string;
  }
}
if(typeof indexOf === 'undefined') {
  function indexOf(el) {
    return Array.from(el.parentElement.children).indexOf(el)
  }
}
if(typeof dynamicListener === 'undefined') {
  function dynamicListener(events, selector, handler, context){
    events.split(' ').forEach(function (event) {
      (document || context).addEventListener(event, function (e) {
        if(e.target.matches(selector) || e.target.closest(selector)){
          handler.call(e.target.closest(selector), e);
        }
      })
    })
  }
}
if(typeof trigger === 'undefined') {
  function trigger(el, eventName, params = {}, bubbles=true) {
    //trigger('click',  undefined,   undefined)
    //trigger('click',  {},          undefined)
    //trigger(el,       'click',     undefined)
    //trigger(el,       'click',     {})
    // passed data will be at e.detail (JS), and e.originalEvent.detail (jQuery)
    let thisEl = el
    let thisEventName = eventName
    let thisParams = params

    if(typeof el === 'string'){
      thisEventName = el
      thisEl = document
      if(typeof eventName === 'object'){
        thisParams = eventName
      }
    }

    let newEvent = new CustomEvent(thisEventName, { bubbles: bubbles, detail: thisParams });
    thisEl.dispatchEvent(newEvent)
  }
}
if(typeof animateOpacity === 'undefined') {
  function animateOpacity(element, duration, display = 'block', targetOpacity, onComplete) {
    const elStyles = window.getComputedStyle(element)
    const startOpacity = elStyles.display === 'none' ? 0 : parseFloat(elStyles.opacity)
    let currentOpacity = startOpacity;

    if(startOpacity !== 1) {
      duration = duration - (duration * startOpacity);
    }

    if (targetOpacity) {
      element.style.opacity = startOpacity
      element.style.display = display
    }

    if (element.animation && element.animation.stop) {
      element.animation.stop()
    }

    function updateOpacity() {
      const elapsedTime = performance.now() - startTime;
      const progress = Math.min(1, elapsedTime / duration);
      currentOpacity = startOpacity + progress * (targetOpacity - startOpacity);
      element.style.opacity = currentOpacity.toFixed(2);

      if (progress < 1) {
        element.animation.raf = requestAnimationFrame(updateOpacity);
      } else {
        element.style.opacity = ''
        element.animation = false
        if (!targetOpacity) {
          element.style.display = 'none'
        }
        if (onComplete) {
          onComplete();
        }
      }
    }

    function stopAnimation() {
      cancelAnimationFrame(element.animation.raf);
    }

    const startTime = performance.now();
    element.animation = {
      raf: 0,
      type: targetOpacity ? 'fadeIn' : 'fadeOut',
      stop: stopAnimation,
    };
    updateOpacity();
  }
}
if(typeof fadeIn === 'undefined') {
  function fadeIn(el, timeout, display = 'block', afterFunc = false) {
    animateOpacity(el, timeout, display, 1, afterFunc);
  }
}
if(typeof fadeOut === 'undefined') {
  function fadeOut(el, timeout, afterFunc = false) {
    animateOpacity(el, timeout, '', 0, afterFunc);
  }
}
if(typeof fadeToggle === 'undefined') {
  function fadeToggle(target, duration = 300, display = 'block', afterFunction = false) {
    if ((target.animation && target.animation.type === 'fadeOut') || (!target.animation && window.getComputedStyle(target).display === 'none')) {
      return fadeIn(target, duration, display, afterFunction);
    } else {
      return fadeOut(target, duration, afterFunction);
    }
  }
}

function validate(form, newOpts = {}) {
  if(!hasPluginRequirements('validate', ['printf', 'selectAll'])){
    return;
  }
  let defaultOpts = {
    methodsOnInput: ['regexReplace', 'maxlength'],
    submitFunction: null,
    highlightFunction: null,
    unhighlightFunction: null,
    checkOnInput: false,
    checkOnInputAfterSubmit: true,
    checkOnFocusOut: true,
    disableButton: false,
    errorClass: 'is-error',
    dontValidateInputs: 'input:not([type="hidden"])[name], .output_value, select, textarea',
    inputContainerSelector: '.input',
    formErrorBlock: '',
    addInputErrors: true,
    validationRules: typeof validationRules !== 'undefined' ? validationRules : {},
    validationErrors: typeof validationErrors !== 'undefined' ? validationErrors : {},
    methods: {
      "regex": function (value, element, regexp) {
        return value == '' || new RegExp(regexp).test(value);
      },
      "required": function (value, input) {
        if(input.getAttribute('type') === 'checkbox' || input.getAttribute('type') === 'radio'){
          let elseInputs = Array.from(form.querySelectorAll(`[name="${input.getAttribute('name')}"]`))
          let hasChecked = !!elseInputs.find(item => item.checked)

          if(hasChecked){
            elseInputs.forEach(function (elseInput) {
              if(typeof elseInput.removeError === 'function') {
                elseInput.removeError()
              }
            })
          }

          return hasChecked
        } else {
          return !!value.trim()
        }
      },
      "regexReplace": function (value, element, regexp) {
        element.value = element.value.replace(new RegExp(regexp), "");
        return true;
      },
      "password_repeat": function (value, element, regexp) {
        let password = element.closest('form').querySelector('[data-validation="password"]');
        return !element.hasAttribute('required') && !value || value === password.value;
      },
      "tel_mask": function (value, element, regexp) {
        if (typeof element['checkValidCallback'] !== 'undefined') {
          element.checkValidCallback();
        }
        return typeof element['telMask'] !== 'undefined' ? element['telMask'].isValidNumber() || value === '' : true;
      },
      "minlength": function (value, element, passedValue) {
        let min = passedValue || +element.getAttribute("minlength");

        if (!min || !value) return true;
        return value.length >= min;
      },
      "maxlength": function (value, element, regexp) {
        let max = +element.getAttribute("maxlength");
        if (!max) return true;
        if (element.value.length > max) {
          element.value = element.value.substr(0, max);
        }
        return true;
      }
    }
  };
  let opts = {
    ...defaultOpts,
    ...newOpts
  };
  if(typeof validationMethods === 'object') {
    opts["methods"] = {
      ...opts["methods"],
      ...validationMethods
    }
  }
  if (typeof form === 'string') form = document.querySelector(form);

  function getMethodError(input, methodName, defaultText, variable = []) {
    let dataValidation = input.getAttribute('data-validation');
    let errorMessage = printf(defaultText, variable)

    if(opts.validationErrors[methodName]){
      errorMessage = printf(opts.validationErrors[methodName], variable)
    }
    if(opts.validationErrors[dataValidation] && opts.validationErrors[dataValidation][methodName]){
      errorMessage = printf(opts.validationErrors[dataValidation][methodName], variable)
    }

    return errorMessage;
  }

  function formSubmitListener(e) {
    e.preventDefault();
    _this.validate();
    _this.formSubmitted = true;
  }
  function inputInputListener(e) {
    this['had_input'] = true;
    if(opts.disableButton){
      _this.checkDisableButton()
    }
    if (opts.methodsOnInput.length) {
      _this.valid(this, opts.methodsOnInput);
      return;
    }
    if (opts.checkOnFocusOut && input['had_focusout']) {
      _this.valid(this);
      return;
    }
    if (opts.checkOnInput) {
      _this.valid(this);
      return;
    }
    if (opts.checkOnInputAfterSubmit && _this.formSubmitted) {
      _this.valid(this);
    }

    let inputsSameName = Array.from(form.querySelectorAll(`[name="${this.getAttribute('name')}"]`))

    if(inputsSameName.length > 1){
      let isTypesSame = !inputsSameName.find(item => item.getAttribute('type') !== inputsSameName[0].getAttribute('type'))
      let hasRequired = inputsSameName.find(item => typeof item.getAttribute('required') !== 'undefined')

      if(!isTypesSame && hasRequired) {
        if(this.getAttribute('type') !== 'checkbox' && this.getAttribute('type') !== 'radio'){
          let diffInputs = inputsSameName.filter(item => item.getAttribute('type') !== this.getAttribute('type'))

          if(diffInputs.length){
            if(this.value.trim()){
              diffInputs.forEach(item => item.isValid())
            }
          }
        }
      }
    }
  }
  function inputFocusListener(e) {
    let inputsSameName = Array.from(form.querySelectorAll(`[name="${this.getAttribute('name')}"]`))

    if(opts.disableButton){
      _this.checkDisableButton()
    }
    if(inputsSameName.length > 1){
      let isTypesSame = !inputsSameName.find(item => item.getAttribute('type') !== inputsSameName[0].getAttribute('type'))
      let hasRequired = inputsSameName.find(item => typeof item.getAttribute('required') !== 'undefined')

      if(!isTypesSame && hasRequired) {
        if(this.getAttribute('type') !== 'checkbox' && this.getAttribute('type') !== 'radio'){
          let diffInputs = inputsSameName.filter(item => item.getAttribute('type') !== this.getAttribute('type'))

          if(diffInputs.length){
            diffInputs.forEach(item => item.removeRequired())
            diffInputs.forEach(item => item.checked = false)
            diffInputs.forEach(item => item.isValid())
          }
          this.setRequired()
        }
      }
    }
  }
  function inputFocusoutListener(e) {
    if(opts.disableButton){
      _this.checkDisableButton()
    }
    if (!opts.checkOnInput && opts.checkOnFocusOut) {
      this['had_focusout'] = true;
      if (!this['had_focusout'] || !this['had_input']) return;
      _this.valid(this);
    }
  }
  function inputChangeListener(e) {
    if(opts.disableButton){
      _this.checkDisableButton()
    }
    if (this.getAttribute('type') === 'checkbox' || this.getAttribute('type') === 'radio' ) {
      this.isValid()
    }


    let inputsSameName = Array.from(form.querySelectorAll(`[name="${this.getAttribute('name')}"]`))

    if(inputsSameName.length > 1){
      let isTypesSame = !inputsSameName.find(item => item.getAttribute('type') !== inputsSameName[0].getAttribute('type'))
      let hasRequired = inputsSameName.find(item => typeof item.getAttribute('required') !== 'undefined')

      if(!isTypesSame && hasRequired) {
        if(this.getAttribute('type') === 'checkbox' || this.getAttribute('type') === 'radio'){
          let diffInputs = inputsSameName.filter(item => item.getAttribute('type') !== this.getAttribute('type'))
          let thisInputs = inputsSameName.filter(item => item.getAttribute('type') === this.getAttribute('type'))
          let oneChecked = thisInputs.find(item => item.checked)

          if(diffInputs.length){
            if(oneChecked){
              diffInputs.forEach(item => item.removeRequired())
              diffInputs.forEach(item => item.isValid())
            } else {
              diffInputs.forEach(item => item.setRequired())
            }
          }
        }
      }
    }
  }
  let _this = {
    isValid: true,
    allInputs: selectAll(opts.dontValidateInputs, form),
    formSubmitted: false,
    init: function () {
      _this.allInputs = selectAll(opts.dontValidateInputs, form)
      form.setAttribute('novalidate', 'novalidate');
      form.setAttribute('data-js-validation', 'novalidate');
      form.addEventListener('submit', formSubmitListener);
      form.valid = function () {
        let addErrors = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
        return _this.validate(false, addErrors);
      };
      _this.allInputs.map(function (input) {
        let thisInputMethods = [];
        let dataValidation = input.getAttribute('data-validation');
        if (input.hasAttribute('required')) {
          thisInputMethods.push({
            callback: opts.methods['required'],
            errorMessage: getMethodError(input, 'required', 'This field is required')
          });
        }
        if (input.hasAttribute('data-tel-mask')) {
          thisInputMethods.push({
            callback: opts.methods['tel_mask'],
            errorMessage: ''
          });
        }
        if (input.hasAttribute('minlength')) {
          thisInputMethods.push({
            callback: opts.methods['minlength'],
            errorMessage: getMethodError(input, 'minlength', 'Min length is &1 symbols', [input.getAttribute('minlength')])
          });
        }
        if (input.hasAttribute('maxlength')) {
          thisInputMethods.push({
            callback: opts.methods['maxlength'],
            errorMessage: getMethodError(input, 'maxlength', 'Max length is &1 symbols', [input.getAttribute('maxlength')])
          });
        }



        // if (input.getAttribute('type') === 'email') {
        //   thisInputMethods.push({
        //     callback: opts.methods['regex'],
        //     passedValue: email_reg,
        //     errorMessage: opts.validationErrors['email']['regex'] || opts.validationErrors['invalid'] || 'This field is invalid'
        //   });
        // }
        if (dataValidation) {
          let thisValidation = opts.validationRules[input.getAttribute('data-validation')];
          if (thisValidation) {
            thisValidation = thisValidation['rules'];
          }
          if (thisValidation) {
            Object.keys(thisValidation).forEach(methodName => {
              let existingMethod = false;
              let thisValidationValue = thisValidation[methodName];
              if (opts.methods[methodName]){
                existingMethod = {
                  callback: opts.methods[methodName],
                  passedValue: thisValidationValue,
                  errorMessage: getMethodError(input, methodName, opts.validationErrors['invalid'] || 'This field is invalid')
                };
              }

              if (existingMethod) thisInputMethods.push(existingMethod);
            });
          }
        }
        function isInputRequired() {
          let removeIt = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
          let thisInputActualMethods = input['validationMethods'];
          let hasRequired = false;
          thisInputActualMethods.map(function (method) {
            if (method.callback.name === 'required') {
              hasRequired = true;
              if (removeIt) thisInputActualMethods.splice(thisInputActualMethods.indexOf(method), 1);
            }
          });
          return hasRequired;
        }
        function setRequired() {
          let thisInputActualMethods = input['validationMethods'];
          if (isInputRequired()) return;
          thisInputActualMethods.push({
            callback: opts.methods['required'],
            errorMessage: getMethodError(input, 'required', 'This field is required')
          });
          input['validationMethods'] = thisInputMethods;
        }
        function removeRequired() {
          isInputRequired(true);
        }
        function setError(message) {
          _this.highlight(input)
          _this.errorPlacement(message, input)
        }
        function removeError() {
          _this.unhighlight(input)
          _this.errorRemove(input)
        }
        input['setError'] = setError
        input['removeError'] = removeError
        input['setRequired'] = setRequired;
        input['removeRequired'] = removeRequired;
        input['isRequired'] = isInputRequired;
        input['validationMethods'] = thisInputMethods;
        input['had_input'] = false;
        input['had_focusout'] = false;
        input['isValid'] = function () {
          return _this.valid(input);
        };
        input.addEventListener('input', inputInputListener);
        input.addEventListener('change', inputChangeListener);
        input.addEventListener('focus', inputFocusListener);
        input.addEventListener('focusout', inputFocusoutListener);
      });
      if (opts['rules']) {
        Object.keys(opts['rules']).forEach(function (rule) {
          let input = document.querySelector('[name="' + rule + '"]');
          let thisRuleValue = opts['rules'][rule];
          let thisInputMethods = input['validationMethods'] || [];
          if (!input) return;
          if (thisRuleValue['laravelRequired']) thisRuleValue = 'required';
          let thisRuleMessage = getMethodError(input, thisRuleValue, opts.validationErrors['invalid'] || 'This field is invalid')
          if (opts['messages'] && opts['messages'][rule] && (opts['messages'][rule][thisRuleValue] || opts['messages'][rule]['laravelRequired'])) thisRuleMessage = opts['messages'][rule][thisRuleValue] || opts['messages'][rule]['laravelRequired'];
          if (opts.methods[thisRuleValue]) {
            thisInputMethods.push({
              callback: opts.methods[thisRuleValue],
              errorMessage: thisRuleMessage
            });
            input['validationMethods'] = thisInputMethods;
          }
        });
      }

      if(opts.disableButton){
        _this.checkDisableButton()
      }

      _this.updateDefaultFormData()
    },
    destroy: function () {
      form.removeAttribute('novalidate', 'novalidate');
      form.removeAttribute('data-js-validation', 'novalidate');
      form.removeEventListener('submit', formSubmitListener);
      form.valid = null;
      _this.allInputs.map(function (input) {
        input['setError'] = null
        input['removeError'] = null
        input['setRequired'] = null;
        input['removeRequired'] = null;
        input['isRequired'] = null;
        input['validationMethods'] = null;
        input['had_input'] = false;
        input['had_focusout'] = false;
        input['isValid'] = null
        input.removeEventListener('input', inputInputListener);
        input.removeEventListener('change', inputChangeListener);
        input.removeEventListener('focus', inputFocusListener);
        input.removeEventListener('focusout', inputFocusoutListener);
      });
    },
    valid: function (input) {
      if(input['dont-check']){
        return true;
      }
      let checkMethods = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
      let addError = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
      let thisMethods = input['validationMethods'];
      if (!thisMethods) return true;
      let isInputValid = true;
      if (checkMethods.length) {
        thisMethods = [];
        checkMethods.forEach(function (thisMethod) {
          let thisInputMethod = input['validationMethods'].find(obj => obj.callback.name === thisMethod);
          if (thisInputMethod) {
            thisMethods.push(thisInputMethod);
          }
        });
      }
      thisMethods.forEach(function (thisMethod) {
        if (!isInputValid) return;
        let isThisValid = thisMethod['callback'](input.value, input, thisMethod['passedValue']);
        if (!isThisValid) {
          if (addError) {
            _this.errorPlacement(thisMethod['errorMessage'], input);
            _this.highlight(input);
          }
          _this.isValid = isInputValid = input['validity']['valid'] = false;
        }
      });
      if (isInputValid) {
        _this.errorRemove(input);
        _this.unhighlight(input);
        input['validity']['valid'] = true;
      }
      return isInputValid;
    },
    validate: function () {
      let submit = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      let addError = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
      _this.isValid = true;
      _this.allInputs.map(function (input) {
        if (!_this.valid(input, addError)) {
          _this.isValid = false;
        }
      });
      if (_this.isValid){
        form.classList.remove('has-error')
        if(submit) {
          _this.submitHandler();
        }
      } else {
        form.classList.add('has-error')
      }
      return _this.isValid;
    },
    highlight: function (element) {
      if (typeof opts.highlightFunction === 'function') {
        opts.highlightFunction(form, element);
        return;
      }
      let container;
      if(typeof opts.inputContainerSelector === 'object'){
        opts.inputContainerSelector.forEach(function (item) {
          if(element.closest(item)) {
            container = element.closest(item)
          }
        })
      } else {
        container = element.closest(opts.inputContainerSelector)
      }
      if (container) container.classList.add(opts.errorClass);
    },
    unhighlight: function (element) {
      if (typeof opts.unhighlightFunction === 'function') {
        opts.unhighlightFunction(form, element);
        return;
      }
      let container;
      if(typeof opts.inputContainerSelector === 'object'){
        opts.inputContainerSelector.forEach(function (item) {
          if(element.closest(item)) {
            container = element.closest(item)
          }
        })
      } else {
        container = element.closest(opts.inputContainerSelector)
      }
      if (container) container.classList.remove(opts.errorClass);
    },
    updateDefaultFormData: function () {
      let submBtn = form.querySelector('[type="submit"]')
      if(!submBtn){
        return;
      }

      _this.defaultFormData = new FormData(form)
    },
    checkDisableButton: function () {
      let submBtn = form.querySelector('[type="submit"]')
      if(!submBtn || !form.querySelector('input:not([type="hidden"])')){
        return;
      }
      let currentFormData = new FormData(form)
      let formHasChanges = false
      let formIsValid = form.valid(false)

      if(typeof _this.defaultFormData !== 'undefined') {
        for (let [key, value] of currentFormData.entries()) {
          if (!_this.defaultFormData.get(key) || _this.defaultFormData.get(key) !== value) {
            formHasChanges = true
          }
        }
      }

      if(formIsValid && formHasChanges){
        submBtn.removeAttribute('disabled')
      } else {
        submBtn.setAttribute('disabled', 'disabled')
      }
    },
    errorPlacement: function (error, element) {
      if (!error) return;
      let container;
      if(typeof opts.inputContainerSelector === 'object'){
        opts.inputContainerSelector.forEach(function (item) {
          if(element.closest(item)) {
            container = element.closest(item)
          }
        })
      } else {
        container = element.closest(opts.inputContainerSelector)
      }
      let formErrorBlock = opts.formErrorBlock ? form.querySelector(opts.formErrorBlock) : false
      if(formErrorBlock){
        if(!formErrorBlock.querySelector(`[data-name="${element.getAttribute('name')}"]`)) {
          formErrorBlock.innerHTML += `<p data-name="${element.getAttribute('name')}">${error}</p>`;
        }
      }
      if (!container){ console.warn('BLACKBOOK Validate: no container for: ', element, opts.inputContainerSelector); return; }

      if(opts.addInputErrors) {
        let errorEl = container.querySelector('.input__message');
        if (!errorEl) {
          errorEl = document.createElement('div');
          errorEl.classList.add('input__message');
          container.append(errorEl);
        }
        errorEl.innerHTML = `<p>${error}</p>`;
      }
    },
    errorRemove: function (element) {
      let container;
      if(typeof opts.inputContainerSelector === 'object'){
        opts.inputContainerSelector.forEach(function (item) {
          if(element.closest(item)) {
            container = element.closest(item)
          }
        })
      } else {
        container = element.closest(opts.inputContainerSelector)
      }
      let formErrorBlock = opts.formErrorBlock ? form.querySelector(opts.formErrorBlock) : false
      if(formErrorBlock){
        if(formErrorBlock.querySelector(`[data-name="${element.getAttribute('name')}"]`)) {
          formErrorBlock.querySelector(`[data-name="${element.getAttribute('name')}"]`).remove()
        }
      }
      if(opts.addInputErrors) {
        if (!container) {
          console.warn('BLACKBOOK Validate: no container for: ', element, opts.inputContainerSelector);
          return;
        }
        container = container.querySelector('.input__message');
        if (!container) return;
        container.innerHTML = '';
      }
    },
    submitHandler: function () {
      if (typeof opts.submitFunction === 'function') {
        opts.submitFunction(form);
      } else {
        form.submit();
      }
    }
  };

  if (form.hasAttribute('data-js-validation')){
    _this.destroy();
    _this.init();
  } else {
    _this.init();
  }
  form.validateMethods = _this;
  return _this;
}


function formReset(form){
  if (typeof form.dataset.noReset !== 'undefined' || form.classList.contains('no-reset')) {
    return;
  }
  form.reset();
  form.querySelectorAll('.is-selected').forEach(item=>item.classList.remove('is-selected'))
  form.querySelectorAll('.image-preview').forEach(item=>item.remove())
  form.querySelectorAll('.ql-editor').forEach(item=>item.innerHTML = '')
  form.querySelectorAll('.is-visible').forEach(item=>item.classList.remove('is-visible'))

  form.querySelectorAll('.output_text').forEach(function (item) {
    let inputContainer = item.closest('.input')
    let defaultValue = inputContainer.querySelector('.is-default')
    let outValue = inputContainer.querySelector('.output_value')

    if(defaultValue){
      defaultValue.classList.add('is-selected')
    }

    if(item.nodeName.toLowerCase() === 'input') {
      item.value = defaultValue ? defaultValue.textContent.trim() : ''
    } else {
      item.textContent = defaultValue ? defaultValue.textContent.trim() : ''
    }
    outValue.value = defaultValue ? defaultValue.dataset.value : ''
  })
}
function ajaxSuccess(form, data){
  data = data['responseJSON'] || data['data'] || data
  let formId = form.getAttribute('id')
  let popupSuccess = form.dataset.successPopup;
  let formBtns = Array.from(form.querySelectorAll('[type="submit"]')).concat(formId ? Array.from(document.querySelectorAll(`[form="${formId}"]`)) : []);
  let thisSection = form.closest('.show-hide-on-success') || form.closest('section') || form
  let showOnSuccess = thisSection.querySelector('.show-on-success')
  let hideOnSuccess = thisSection.querySelector('.hide-on-success')
  let redirectUrl = data["redirect_url"] || data["redirect"] || form.dataset.redirect

  if (form['ajaxSuccess']){
    form['ajaxSuccess'](form, data)
  }

  // Fancybox.close();

  if (redirectUrl) {
    window.location.href = redirectUrl;
    return;
  }

  formBtns.forEach(formBtn => formBtn.removeAttribute('disabled', 'disabled'))



  if (popupSuccess) {
    // Fancybox.show([{
    //   src: popupSuccess,
    //   type: 'inline',
    //   placeFocusBack: false,
    //   trapFocus: false,
    //   autoFocus: false
    // }], {
    //   dragToClose: false
    // });
  }
  if(hideOnSuccess){
    fadeOut(hideOnSuccess, 300, function () {
      if(showOnSuccess) {
        fadeIn(showOnSuccess, 300)
      }
    })
  } else if(showOnSuccess){
    fadeIn(showOnSuccess, 300)
  }



  formReset(form)
}
function ajaxError(form, data){
  data = data['responseJSON'] || data['data'] || data
  let formId = form.getAttribute('id')
  let formBtns = Array.from(form.querySelectorAll('[type="submit"]')).concat(formId ? Array.from(document.querySelectorAll(`[form="${formId}"]`)) : []);
  let popupError = form.dataset.errorPopup;

  if (form['ajaxError']){
    form['ajaxError'](form, data)
  }

  if (!form.dataset.listenerAdded) {
    form.addEventListener('input', () => clearFormErrors(form));
    form.addEventListener('change', () => clearFormErrors(form));
    form.dataset.listenerAdded = 'true';
  }

  function clearFormErrors() {
    const errorMessages = form.querySelector('.main_form_message');
    if (errorMessages) {
      errorMessages.innerHTML = '';
      errorMessages.style.display = 'none';
    }
    form.classList.remove('has-error');
  }

  // if(!form.dataset.nofancycloseonerror) Fancybox.close();

  formBtns.forEach(formBtn => formBtn.removeAttribute('disabled', 'disabled'))

  if(typeof data === 'object' && data['errors']){
    let scrolledToInput = false
    form.classList.add('has-error')
    Object.keys(data['errors']).forEach(name => {
      let formInput = form.querySelector(`[name="${name}"]`)
      let errorMessage = data['errors'][name]
      if(formInput) {
        if(form.classList.contains('form--no-errors')){
          form.querySelector('.form__message').style.display = 'block'
          form.querySelector('.form__message').innerHTML += '<p data-name="email">'+errorMessage+'</p>'
        } else {
          if (formInput['setError']) {
            formInput.setError(errorMessage)
            if (!scrolledToInput) {
              if (document.scrollTo) {
                document.scrollTo(formInput, 700)
              }
              scrolledToInput = true;
            }
          }
        }
      } else if (name === 'general') {
        // Handle general errors not specific to any input
        form.querySelector('.main_form_message').style.display = 'block';
        form.querySelector('.main_form_message').innerHTML += '<p>' + errorMessage + '</p>';
      }
    })
  } else {
    form.classList.remove('has-error')
    form.querySelector('.form__message').style.display = 'none'
  }
  if (popupError) {
    // Fancybox.show([{
    //   src: popupError,
    //   type: 'inline',
    //   placeFocusBack: false,
    //   trapFocus: false,
    //   autoFocus: false
    // }], {
    //   dragToClose: false
    // });
  }
}
function onSubmit(form, thisFormData = false) {
  let formData = thisFormData || new FormData(form);
  let formId = form.getAttribute('id')
  let action = form.getAttribute('action') || '/wp-admin/admin-ajax.php';
  let method = form.getAttribute('method') || 'post';
  let formBtns = Array.from(form.querySelectorAll('[type="submit"]')).concat(formId ? Array.from(document.querySelectorAll(`[form="${formId}"]`)) : []);
  let editors = form.querySelectorAll('.ql-editor');
  let formMessages = form.querySelector('.form__message')
  let xhr = new XMLHttpRequest();
  let csrfToken = document.querySelector('meta[name="csrf-token"]')

  if (editors.length) {
    editors.forEach(function(editor) {
      let thisName = editor.closest('[data-name]');
      editor.querySelectorAll('.ql-emojiblot').forEach(function(emoji){
        emoji.outerHTML = emoji.textContent
      })
      let thisValue = editor.innerHTML;
      if (!thisName) return;
      thisName = thisName.dataset.name;
      formData.append(thisName, thisValue);
    });
  }

  formBtns.forEach(formBtn => formBtn.setAttribute('disabled', 'disabled'))

  if(formMessages){
    formMessages.innerHTML = ''
    formMessages.style.display = 'none'
  }
  form.classList.remove('has-error')
  xhr.open(method, action);
  if(csrfToken) {
    xhr.setRequestHeader("csrf_token", csrfToken.getAttribute("content"));
  }
  xhr.send(formData);
  xhr.onload = function() {
    let data = xhr.responseText
    try { data = JSON.parse(data) } catch (error) {}
    if (xhr.status === 200) {
      ajaxSuccess(form, data)
    } else {
      ajaxError(form, data)
    }
  };
}

function fadingPages(context, opts) {
  let pages = Array.from(context.querySelectorAll(opts.pageSelector))
  let searchParams = window.currentUrl.searchParams
  let activePageName = searchParams.get(opts.getParameter) && pages.find(item => item.dataset.page === searchParams.get(opts.getParameter)) ? searchParams.get(opts.getParameter) : 'main'
  let activePage = null

  function showPage(pageName) {
    let newPage = pages.find(item=>item.dataset.page === pageName)
    if(!newPage){
      console.warn('No page found by name: '+pageName)
      return;
    }
    if(activePage){
      fadeOut(activePage, opts.timing, function () {
        fadeIn(newPage, opts.timing)
      })
      activePage = newPage
    } else {
      activePage = newPage
      newPage.style.display = 'block'
    }


    trigger(activePage, 'show')

    searchParams = new URLSearchParams(document.location.search)

    if(searchParams.get(opts.getParameter) !== pageName) {
      searchParams.set(opts.getParameter, pageName)

      window.history.pushState({}, document.title, document.location.pathname + (searchParams.size ? '?' + searchParams.toString() : ''));
    }
  }

  showPage(activePageName)
  dynamicListener('click', '[data-page-link]', function (e) {
    e.preventDefault()
    showPage(this.dataset.pageLink)
  })
  dynamicListener('click', '[data-page-back]', function (e) {
    e.preventDefault()
    window.history.back()
  })
  window.addEventListener('popstate', (e,r,t,y) => {
    let pageName = e.target.currentUrl.searchParams.get(opts.getParameter)

    if(activePage.dataset.page === pageName){
      window.history.back()
      return;
    }
    if(pageName){
      showPage(pageName)
    } else {
      showPage('main')
    }
  });

  return {
    show: showPage
  }
}

function blocks() {
  let methods = {
    '.header': function () {
      window.addEventListener('scroll', function () {
        if(window.scrollY > 10){
          document.querySelector('.header').classList.add('is-scrolled')
        } else {
          document.querySelector('.header').classList.remove('is-scrolled')
        }
      })
    },
    '.input--select': function() {
      dropdown({
        globalContainer: '',
        containerClass: 'input--select',
        btnSelector: '.output_text',
        closeBtnClass: '',
        dropdownSelector: '.input__dropdown',
        effect: 'fade',
        timing: 200
      });

      function selectItem(e) {
        let option = e.target;
        if(e['nodeName']) option = e;
        if(option.classList.contains('input__search') || option.querySelector('a')) return;
        let container = option.closest('.input--select');
        let text = container.classList.contains('output-html') ? option.innerHTML.trim() : option.textContent.trim();
        let value = option.dataset.value;
        let outText = container.querySelector('.output_text');
        let outValue = container.querySelector('.output_value');

        if(!container.classList.contains('no-output')) {
          if(outText && text) {
            if(outText.nodeName.toLowerCase() === 'input') {
              outText.value = text;
            } else {
              outText.innerText = text;
            }
          }
        }
        if(outValue) {
          outValue.value = value;
          if(typeof outValue.isValid === 'function'){
            outValue.isValid();
          }
          if(typeof trigger === 'function'){
            trigger(outValue, 'change')
          }
        }
        option.classList.add('is-selected');

        Array.from(option.parentElement.children).forEach(function (item) {
          if(item != option){
            item.classList.remove('is-selected')
          }
        })

        if(!container.classList.contains('has-checkbox') && typeof trigger === 'function') {
          trigger('close-dropdown')
        }
      }
      function searchList(thisInput) {
        let thisItems = thisInput.closest('.input--select').querySelectorAll('li');
        let thisVal = thisInput.value.trim().toLowerCase();

        thisItems.forEach(item=>item.style.display = 'none')
        thisItems.forEach(item=>item.textContent.trim().toLowerCase().indexOf(thisVal) > -1 ? item.style.display = '' : '')
      }

      dynamicListener('click', '.input--select li', selectItem)
      dynamicListener('update', '.input--select .output_value', function(e) {
        let outInp = e.target
        let container = outInp.closest('.input')
        let findItem = container.querySelector(`[data-value="${outInp.value}"]`)
        if(findItem){
          selectItem(findItem)
        }
      })
      dynamicListener('input', '.input--select input.output_search', function(e) {
        searchList(e.target)
      });
      document.querySelectorAll('.input--select .is-selected, .input--select .is-default').forEach(function(input) {
        selectItem(input);
      });
    },
    '.form': function (forms) {
      forms.forEach(function (form) {
        let validationOpts = {
          submitFunction: onSubmit
        }
        let passwordShowBtns = form.querySelectorAll('.js-toggle-password')
        let undoSuccessBtns = form.closest('.show-hide-on-success') ? form.closest('.show-hide-on-success').querySelectorAll('.js-undo-success') : []

        passwordShowBtns.forEach(function (thisBtn) {
          thisBtn.addEventListener('click', function (e) {
            e.preventDefault()
            let thisContainer = this.closest('.input')
            if(!thisContainer){
              return;
            }
            let thisInput = thisContainer.querySelector('input')
            if(!thisInput){
              return;
            }

            if(thisInput.getAttribute('type') === 'password'){
              thisInput.setAttribute('type', 'text')
            } else {
              thisInput.setAttribute('type', 'password')
            }
          })
        })
        undoSuccessBtns.forEach(function (thisBtn) {
          thisBtn.addEventListener('click', function (e) {
            e.preventDefault()
            let thisContainer = this.closest('.show-hide-on-success')
            if(!thisContainer){
              return;
            }
            let showOnSuccess = thisContainer.querySelector('.show-on-success')
            let hideOnSuccess = thisContainer.querySelector('.hide-on-success')
            if(!showOnSuccess || !hideOnSuccess){
              return;
            }

            fadeOut(showOnSuccess, 300, function () {
              fadeIn(hideOnSuccess, 300)
            })
          })
        })

        if(form.classList.contains('form--no-errors')){
          validationOpts['addInputErrors'] = false
          validationOpts['formErrorBlock'] = '.form__message'
          validationOpts['disableButton'] = form.classList.contains('form--disable-btn')
        }
        validate(form, validationOpts)
      })
    },
    '.warehouse': function (sections) {
      let section = sections[0]
      let barcodeInput = document.getElementById('barcode-input');
      let currentBarcodeInstance;
      let sounds = {
        attention: new Audio(template_path+'/assets/redesign/audio/attention.wav'),
        incorrect: new Audio(template_path+'/assets/redesign/audio/incorrect.wav'),
      }
      let pagesCallbacks = {
        'pick': function (pickPage) {
          let modals = {
            'queue': document.getElementById('warehouse-pick-queue'),
            'quantity': document.getElementById('warehouse-pick-quantity'),
            'barcode': document.getElementById('warehouse-pick-barcode'),
          }

          let inactivityTimeout;
          let recheckQueueTimeout;

          function showPickQueuePopup(openPopup = true) {
            let num = modals.queue.querySelector('.js-pick-num')
            let btn = modals.queue.querySelector('.js-pick-start')
            let formData = new FormData();
            let xhr = new XMLHttpRequest();

            clearTimeout(recheckQueueTimeout)

            btn.setAttribute('disabled', 'disabled')
            modals.queue.classList.add('is-loading')
            modals.queue.classList.remove('can-close')

            if(openPopup) {
              Fancybox.show([{
                src: '#' + modals.queue.id,
                type: 'inline',
                placeFocusBack: false,
                trapFocus: false,
                autoFocus: false,
              }], {
                dragToClose: false,
                on: {
                  "destroy": (event, fancybox, slide) => {
                    if (!modals.queue.classList.contains('can-close')) {
                      pagesInstance.show('main')
                    }
                    clearTimeout(recheckQueueTimeout)
                  },
                }
              });
            }
            formData.append('action', 'get_orders_in_queue')

            xhr.open('post', '/wp-admin/admin-ajax.php');
            xhr.send(formData);
            xhr.onload = function() {
              let data = xhr.responseText
              try { data = JSON.parse(data) } catch (error) {}
              if (xhr.status === 200) {
                num.textContent = data.data.count
                if(data.data.count > 0) {
                  btn.removeAttribute('disabled')
                }

                recheckQueueTimeout = setTimeout(function () {
                  showPickQueuePopup(false)
                }, 10000)
                modals.queue.classList.remove('is-loading')
              }
            };
          }
          function showPickIncorrectBarcode(tryAgainFunction = ()=>{}) {
            Fancybox.show([{
              src: '#'+modals.barcode.id,
              type: 'inline',
              placeFocusBack: false,
              trapFocus: false,
              autoFocus: false,
            }], {
              dragToClose: false,
              on: {
                "destroy": (event, fancybox, slide) => {
                  tryAgainFunction()
                },
              }
            });
          }
          function showPickQuantityPopup(quantity = 2, afterFunction = ()=>{}) {
            modals.quantity.querySelector('.js-pick-quantity').innerHTML = 'x<span>'+quantity+'</span>'

            Fancybox.show([{
              src: '#'+modals.quantity.id,
              type: 'inline',
              placeFocusBack: false,
              trapFocus: false,
              autoFocus: false,
            }], {
              dragToClose: false,
              on: {
                "destroy": (event, fancybox, slide) => {
                  afterFunction()
                },
              }
            });
          }

          function startPick(order) {
            let activeItem

            let counter = 1;
            pickPage.insertAdjacentHTML('beforeend', `
            <div class="warehouse__pick" data-order-id="${order.id}">
              <div class="warehouse__pick-info">
                <p><strong>Order #${order.id}</strong></p>
                <p>${order.name}</p>
                <p>${order.address_1}</p>
                <p>${order.address_2}</p>
              </div>
              <div class="warehouse__pick-items">
              ${Object.entries(order.items).map(([key, item], index) => {
                if (typeof item === 'object' && !item.item_name) {
                  return Object.entries(item).map(([subKey, subItem], subIndex) => `
                    <div class="warehouse__pick-item" data-barcode="${subItem.barcode}" data-quantity="${subItem.quantity}" ${index === 0 && subIndex === 0 ? 'style="display: block"' : ''}>
                      <div class="warehouse__pick-title">
                        <span>Item ${counter++} of ${order.total_items}</span>
                      </div>
                      <div class="warehouse__pick-img">
                        <img src="${subItem.img || '#'}" alt="${subItem.item_name}">
                      </div>
                      <div class="warehouse__pick-name">
                        <span>${subItem.item_name}</span>
                      </div>
                      <div class="warehouse__quantity ${subItem.quantity > 1 ? 'warehouse__quantity--red' : ''}">
                        x<span>${subItem.quantity}</span>
                      </div>
                      <div class="warehouse__pick-subtitle">
                        <span>SCAN BARCODE TO CONFIRM</span>
                      </div>
                    </div>
                  `).join('');
                }
                // For normal items
                return `
                  <div class="warehouse__pick-item" data-barcode="${item.barcode}" data-quantity="${item.quantity}" ${index === 0 ? 'style="display: block"' : ''}>
                    <div class="warehouse__pick-title">
                      <span>Item ${counter++} of ${order.total_items}</span>
                    </div>
                    <div class="warehouse__pick-img">
                      <img src="${item.img || '#'}" alt="${item.item_name}">
                    </div>
                    <div class="warehouse__pick-name">
                      <span>${item.item_name}</span>
                    </div>
                    <div class="warehouse__quantity ${item.quantity > 1 ? 'warehouse__quantity--red' : ''}">
                      x<span>${item.quantity}</span>
                    </div>
                    <div class="warehouse__pick-subtitle">
                      <span>SCAN BARCODE TO CONFIRM</span>
                    </div>
                  </div>
                `;
              }).join('')}
            
            <div class="warehouse__pick-item js-printing-step">
              <div class="warehouse__pick-title">
                <span>Ready for Packing Slip</span>
              </div>
              <div class="warehouse__pick-img">
                <img src="${template_path}/assets/images/ico-printer.svg">
              </div>
              <div class="warehouse__pick-name">
                <span>SCAN PRINTER<br> BARCODE NOW FOR<br> PACKING SLIP</span>
              </div>
              <div class="warehouse__pick-btn">
                <button type="button" class="btn js-pick-complete">COMPLETE</button>
              </div>
            </div>
            <div class="warehouse__pick-btn">
              <button type="button" class="btn btn--transparent js-pick-return">RETURN ORDER TO QUEUE</button>
            </div>
          </div>`)
            activeItem = pickPage.querySelector('.warehouse__pick-item')
            activeItem.classList.add('is-active')

            function resetInactivityTimer() {
              clearTimeout(inactivityTimeout); 
              inactivityTimeout = setTimeout(() => {
                  sessionExpired();
              }, INACTIVITY_LIMIT);
            }
        
            function sessionExpired() {
              
              let formData = new FormData();
              let xhr = new XMLHttpRequest();

              formData.append('action', 'order_session_expired');
              formData.append('order_id', order.id);

              xhr.open('post', '/wp-admin/admin-ajax.php');
              xhr.send(formData);
              xhr.onload = function() {
                let data = xhr.responseText
                try { data = JSON.parse(data) } catch (error) {}
                if (xhr.status === 200) {
                  alert('The session for orders has expired.');
                  let existingEl = pickPage.querySelector('.warehouse__pick')
  
                  if(existingEl){
                    existingEl.remove()
                  }
                  if(currentBarcodeInstance){
                    currentBarcodeInstance.destroy()
                  }
  
                  warehouse_active_pick_order = {}
                  pagesInstance.show('main')
                }
              };
            }

            function sendPrinterRequest(barcode) {
              resetInactivityTimer();
              let formData = new FormData();
              let xhr = new XMLHttpRequest();

              formData.append('action', 'send_print_request')
              formData.append('order_id', order.id)
              formData.append('printer_barcode', barcode)

              xhr.open('post', '/wp-admin/admin-ajax.php');
              xhr.send(formData);
              xhr.onload = function() {
                let data = xhr.responseText
                try { data = JSON.parse(data) } catch (error) {}

                currentBarcodeInstance = waitForBarcode(false, sendPrinterRequest)
              };
            }
            function onBarcodeInput(isCorrect) {
              resetInactivityTimer();

              if(isCorrect){
                let nextItem = pickPage.querySelector('.warehouse__pick-item.is-active + .warehouse__pick-item')

                function continueNext() {
                  activeItem.classList.remove('is-active')
                  nextItem.classList.add('is-active')

                  fadeOut(activeItem, 300, function () {
                    fadeIn(nextItem, 300)
                  })

                  activeItem = nextItem
                  if(!activeItem.classList.contains('js-printing-step')){
                    currentBarcodeInstance = waitForBarcode(activeItem.dataset.barcode, onBarcodeInput)
                  } else {
                    currentBarcodeInstance = waitForBarcode(false, sendPrinterRequest)
                  }
                }

                if(nextItem) {
                  if(activeItem.dataset.quantity && parseInt(activeItem.dataset.quantity) > 1){
                    sounds.attention.play().then(r => {
                      sounds.attention.currentTime = 0
                    })
                    showPickQuantityPopup(activeItem.dataset.quantity, function () {
                      continueNext()
                    })
                  } else {
                    continueNext()
                  }
                }
              } else {
                showPickIncorrectBarcode(function () {
                  currentBarcodeInstance = waitForBarcode(activeItem.dataset.barcode, onBarcodeInput)
                })
              }
            }

            resetInactivityTimer();
            currentBarcodeInstance = waitForBarcode(activeItem.dataset.barcode, onBarcodeInput)
          }

          if(typeof warehouse_active_pick_order === 'object' && warehouse_active_pick_order.id){
            let existingEl = pickPage.querySelector('.warehouse__pick')

            if(existingEl && existingEl.dataset.orderId == warehouse_active_pick_order.id){
              return;
            }
            startPick(warehouse_active_pick_order)
          } else {
            showPickQueuePopup()
          }

          if(pickPage.initted){
            return;
          } else {
            pickPage.initted = true
          }

          dynamicListener('click', '.js-pick-start', function (e) {
            e.preventDefault()
            let btn = this
            let formData = new FormData();
            let xhr = new XMLHttpRequest();

            btn.setAttribute('disabled', 'disabled')
            clearTimeout(recheckQueueTimeout)

            formData.append('action', 'get_order_from_queue')

            xhr.open('post', '/wp-admin/admin-ajax.php');
            xhr.send(formData);
            xhr.onload = function() {
              let data = xhr.responseText
              try { data = JSON.parse(data) } catch (error) {}
              if (xhr.status === 200) {
                modals.queue.classList.add('can-close')
                Fancybox.close()
                btn.removeAttribute('disabled')
                startPick(data.data)
                warehouse_active_pick_order = data.data
              }
            };
          })
          dynamicListener('click', '.js-pick-complete', function (e) {
            e.preventDefault()
            let btn = this
            let orderId = btn.closest('[data-order-id]').dataset.orderId
            let formData = new FormData();
            let xhr = new XMLHttpRequest();

            btn.setAttribute('disabled', 'disabled')

            formData.append('action', 'complete_order')
            formData.append('order_id', orderId)

            xhr.open('post', '/wp-admin/admin-ajax.php');
            xhr.send(formData);
            xhr.onload = function() {
              let data = xhr.responseText
              try { data = JSON.parse(data) } catch (error) {}
              if (xhr.status === 200) {
                clearTimeout(inactivityTimeout);
                let existingEl = pickPage.querySelector('.warehouse__pick')

                if(existingEl){
                  existingEl.remove()
                }
                if(currentBarcodeInstance){
                  currentBarcodeInstance.destroy()
                }

                showPickQueuePopup()
              }

              btn.removeAttribute('disabled')
            };
          })
          dynamicListener('click', '.js-pick-return', function (e) {
            e.preventDefault()
            let btn = this
            let orderId = btn.closest('[data-order-id]').dataset.orderId
            let formData = new FormData();
            let xhr = new XMLHttpRequest();

            btn.setAttribute('disabled', 'disabled')

            formData.append('action', 'return_order_to_queue')
            formData.append('order_id', orderId)

            xhr.open('post', '/wp-admin/admin-ajax.php');
            xhr.send(formData);
            xhr.onload = function() {
              let data = xhr.responseText
              try { data = JSON.parse(data) } catch (error) {}
              if (xhr.status === 200) {
                clearTimeout(inactivityTimeout);
                let existingEl = pickPage.querySelector('.warehouse__pick')

                if(existingEl){
                  existingEl.remove()
                }
                if(currentBarcodeInstance){
                  currentBarcodeInstance.destroy()
                }

                warehouse_active_pick_order = {}
                pagesInstance.show('main')
              }

              btn.removeAttribute('disabled')
            };
          })
        },
        'pack': function (packPage) {
          let modals = {
            'error': document.getElementById('warehouse-error'),
          }
          let scannerBlock = packPage.querySelector('.warehouse__scanner')
          let packBlock = packPage.querySelector('.warehouse__pack')
          let infoEl = packPage.querySelector('.warehouse__pick-info')

          function showPackError(message, tryAgainFunction = ()=>{}) {
            let errorTitle = modals.error.querySelector('.js-error-title')
            let errorText = modals.error.querySelector('.js-error-text')

            if(message.title){
              errorTitle.style.display = ''
              errorTitle.textContent = message.title
            } else {
              errorTitle.style.display = 'none'
            }

            if(message.text){
              errorText.style.display = ''
              errorText.textContent = message.text
            } else {
              errorText.style.display = 'none'
            }

            Fancybox.show([{
              src: '#'+modals.error.id,
              type: 'inline',
              placeFocusBack: false,
              trapFocus: false,
              autoFocus: false,
            }], {
              dragToClose: false,
              on: {
                "destroy": (event, fancybox, slide) => {
                  tryAgainFunction()
                },
              }
            });
          }
          function showPackScanner() {
            if(scannerBlock.style.display === 'none'){
              fadeOut(packBlock, 300, function () {
                fadeIn(scannerBlock, 300)
              })
            }

            currentBarcodeInstance = waitForBarcode(false, onBarcodeInput)
          }
          function startPack(order) {
            fadeOut(scannerBlock, 300, function () {
              fadeIn(packBlock, 300)
            })

            packBlock.classList.remove('small', 'medium', 'large', 'xl', 'lil-brown', 'big-brown')
            packBlock.classList.add(order.box_size)

            packBlock.dataset.orderId = order.id

            infoEl.innerHTML = `
              <p><strong>Order #${order.id}</strong></p>
              <p>${order.name}</p>
              <p>${order.address_1}</p>
              <p>${order.address_2}</p>
            `;
          }

          function onBarcodeInput(barcode) {
            let formData = new FormData();
            let xhr = new XMLHttpRequest();

            packPage.classList.add('is-loading')

            formData.append('action', 'get_packing_list')
            formData.append('packing_barcode', barcode)

            xhr.open('post', '/wp-admin/admin-ajax.php');
            xhr.send(formData);
            xhr.onload = function() {
              let data = xhr.responseText
              try { data = JSON.parse(data) } catch (error) {}
              if (xhr.status === 200) {
                startPack(data.data)
                warehouse_active_pack_order = data.data
              } else {
                showPackError(data.data, function () {
                  currentBarcodeInstance = waitForBarcode(false, onBarcodeInput)
                })
              }
              packPage.classList.remove('is-loading')
            };
          }

          if(typeof warehouse_active_pack_order === 'object' && warehouse_active_pack_order.id){
            if(document.body.dataset.orderId === undefined){
              startPack(warehouse_active_pack_order)
            } else if(packBlock.dataset.orderId == warehouse_active_pack_order.id){
              return;
            } else {
              showPackScanner()
            }
          } else {
            showPackScanner()
          }


          if(packPage.initted){
            return;
          } else {
            packPage.initted = true
          }

          dynamicListener('click', '.js-pack-reprint', function (e) {
            e.preventDefault()
            let btn = this
            let orderId = btn.closest('[data-order-id]').dataset.orderId
            let formData = new FormData();
            let xhr = new XMLHttpRequest();

            btn.setAttribute('disabled', 'disabled')

            formData.append('action', 'send_print_pack_request')
            formData.append('order_id', orderId)

            xhr.open('post', '/wp-admin/admin-ajax.php');
            xhr.send(formData);
            xhr.onload = function() {
              let data = xhr.responseText
              try { data = JSON.parse(data) } catch (error) {}

              if(xhr.status !== 200){
                showPackError(data.data)
              }
              btn.removeAttribute('disabled')
            };
          })
          dynamicListener('click', '.js-pack-complete', function (e) {
            e.preventDefault()
            let btn = this
            let orderId = btn.closest('[data-order-id]').dataset.orderId
            let formData = new FormData();
            let xhr = new XMLHttpRequest();

            btn.setAttribute('disabled', 'disabled')

            formData.append('action', 'complete_pack_order')
            formData.append('order_id', orderId)

            xhr.open('post', '/wp-admin/admin-ajax.php');
            xhr.send(formData);
            xhr.onload = function() {
              let data = xhr.responseText
              try { data = JSON.parse(data) } catch (error) {}
              if (xhr.status === 200) {
                showPackScanner()
              } else {
                showPackError(data.data)
              }

              btn.removeAttribute('disabled')
            };
          })
        },
      }

      function waitForBarcode(barcode=false, afterFunction=()=>{}) {
        let newBarcode = ''

        function destroy() {
          newBarcode = ''
          barcodeInput.value = ''
          barcodeInput.textContent = ''
          document.removeEventListener('click', focusInput)
          barcodeInput.removeEventListener('keydown', inputKeydown);
          currentBarcodeInstance = null
        }
        function focusInput() {
          barcodeInput.focus();
        }
        function inputKeydown(event) {
          if (event.key === 'Enter') {
            event.preventDefault();
            let tempBarcode = newBarcode

            destroy()

            afterFunction(barcode ? barcode == tempBarcode : tempBarcode)
            if(barcode && barcode !== tempBarcode){
              sounds.incorrect.play().then(r => {
                sounds.incorrect.currentTime = 0
              })
            }
          } else if (!isNaN(event.key)) {
            newBarcode += event.key;
          }
        }

        focusInput()

        document.removeEventListener('click', focusInput)
        document.addEventListener('click', focusInput)
        barcodeInput.addEventListener('keydown', inputKeydown);

        return {
          destroy: destroy
        }
      }

      section.addEventListener('show', function (e) {
        console.log(currentBarcodeInstance)
        if(typeof pagesCallbacks[e.target.dataset.page] === 'function'){
          pagesCallbacks[e.target.dataset.page](e.target)
        }
      })

      let pagesInstance = fadingPages(section, {
        pageSelector: '.warehouse__page',
        getParameter: 'warehouse',
        timing: 300
      })
    }
  };
  Object.keys(methods).forEach(selector => {
    if (document.querySelector(selector)) {
      methods[selector](document.querySelectorAll(selector));
    }
  });
}


document.addEventListener('DOMContentLoaded', function () {
  blocks();

  Fancybox.bind('[data-fancybox]', {
    dragToClose: false
  });

// Fancybox.show([{
//     src: '#modal_error',
//     type: 'inline',
//     placeFocusBack: false,
//     trapFocus: false,
//     autoFocus: false,
//   }], {
//     dragToClose: false,
//     on: {
//       "destroy": (event, fancybox, slide) => {
//         clearTimeout(closeTimeout)
//         if(activePopup){
//           openPopup(false, activePopup)
//         }
//       },
//     }
// });
});
