/**
 * "Микроплагины" jQuery.
 * @author dsokolov
 */

/**
 * Добавляет указанный класс к элемент(-ам) на указанное число миллисекунд.
 * @param {String} sClass Имя добавляемого класса
 * @param {Integer} nMilliseconds Число миллисекунд задержки нового класса
 */
jQuery.fn.classForTime = function(sClass, nMilliseconds) {
	return this.each(function() {
		jQuery(this).addClass(sClass);
		var self = this;
		
		setTimeout(function() {
			jQuery(self).removeClass(sClass)
		}, nMilliseconds);
	});
};

/**
* Показывает красивый полёт рамки элемента к указанной цели.
* @param {jQuery} flyTarget Селектор, DOM-нода или jQuery-объект целевого элемента
* @param {Function} endCallback Необязательный коллбэк после пролёта
*/
jQuery.fn.flyElement = function(flyTarget, endCallback) {
	// Некуда лететь? Ку-ку...
	if (typeof flyTarget == 'undefined')
		return this;

	var $eTarget = $(flyTarget);
	var endPosition   = $eTarget.offset();
	var endWidth      = $eTarget.width();
	var endHeight     = $eTarget.height();

	return this.each(function() {
		var startPosition = $(this).offset();
		var startWidth    = $(this).width();
		var startHeight   = $(this).height();

		$('<div />').
			css(
			{
				'position': 'absolute',
				'z-index': 100,
				'border': '2px dotted #404040',
				'display': 'none'
			}).
			width(startWidth).
			height(startHeight).
			css({
				top: startPosition.top + 'px',
				left: startPosition.left + 'px'
			}).
			appendTo('body').
			show().
			animate({
				left: endPosition.left,
				top: endPosition.top,
				width: endWidth,
				height: endHeight
			}, {
				duration: 400,
				complete: function() {
					$(this).remove();
					if (endCallback)
						endCallback();
				}
			});
	});
};


jQuery.fn.lightRoll = function(options)
{
	var _options = {
		color: '#FFE200',
		halftoneColor: '#FFC100',
		duration: 50,
		pause: 3000,
		maxIterations: -1
	};

	jQuery.extend(_options, options);

	var _encodeLetter = function(sString, nLetterPos)
	{
		if (nLetterPos >= sString.length)
			return sString;

		var startHalf = nLetterPos - 1;
		var endHalf = nLetterPos + 1;
		var strlen = sString.length;

		var constHead = sString.substr(0, (startHalf > 0)? startHalf : 0);
		var constTail = (endHalf < strlen)? sString.substr(endHalf + 1) : '';

		var letters = [
			(startHalf >= 0 && startHalf < strlen)?
				'<b style="font-weight: normal; color: ' + _options.halftoneColor + ';">' + sString.charAt(startHalf) + '</b>' : '',
			(nLetterPos >= 0 && nLetterPos < strlen)?
				'<b style="font-weight: normal; color: ' + _options.color + ';">' + sString.charAt(nLetterPos) + '</b>' : '',
			(endHalf >= 0 && endHalf < strlen)?
				'<b style="font-weight: normal; color: ' + _options.halftoneColor + ';">' + sString.charAt(endHalf) + '</b>' : ''
		];

		var result = constHead + letters.join('') + constTail;
		return result;
	};

	return this.each(function() {
		var _itemText = jQuery(this).text();
		var _letter = 0;
		var _pause = 0;
		var _self = this;
		
		var _interval = setInterval(function() {
			if (_pause > 0)
			{
				_pause -= _options.duration;
				return;
			}

			if (_letter >= _itemText.length)
			{
				_letter = 0;
				// Тут нужно вернуть паузу и исходный текст
				jQuery(_self).html(_itemText);
				_pause = _options.pause;

				if (_options.maxIterations > 0)
					_options.maxIterations--;

				if (0 == _options.maxIterations)
					clearInterval(_interval);

				return;
			}

			// А теперь корячим бегунка
			jQuery(_self).html(_encodeLetter(_itemText, _letter));
			_letter++;
		}, _options.duration);
	});
};

jQuery.fn.numeric = function(options)
{
	var _options = {
		keyboard: true,
		min: 1,
		max: 50,
		value: 1,
		forceBounds: false
	};

	jQuery.extend(_options, options);

	return this.each(function() {
		if (typeof HTMLInputElement == 'undefined')
			return;
		
		if (!this instanceof HTMLInputElement)
			return;

		// Если value задан В ПЕРЕДАННОМ, то игнорим содержимое поля
		if (typeof (options.value) != 'undefined')
		{
			var _current = _options.value;
		}
		else
		{
			// Иначе - значение из поля
			var fval = parseInt($(this).val());

			if (!isNaN(fval) && (fval >= _options.min) && (fval <= _options.max))
				_current = fval;
			else
				_current = _options.value;
		}
		
		var _oldValue = _current;

		jQuery(this).
			val(_current).
			keypress(function(event) {
				var code = event.charCode;

				if (code >= 48 && code <= 57)
					return true;

				event.preventDefault();
				return false;
			}).
			keydown(function(event) {
				if (!_options.keyboard)
					return true;
				
				// UP
				if (event.keyCode == 38) {
					_current = (_current < _options.max)? _current + 1 : _current;
					jQuery(this).val(_current);
				}

				// Down
				if (event.keyCode == 40) {
					_current = (_current > _options.min)? _current - 1 : _current;
					jQuery(this).val(_current);
				}
			}).keyup(function(event) {
				// Тут следует проверить переполнение диапазонов
				_current = parseInt(jQuery(this).val());

				if (isNaN(_current)) {
					_current = _oldValue;
					return true;
				}

				if (_current > _options.max || _current < _options.min) {
					if (!_options.forceBounds) {
						_current = _oldValue;
					}
					else {
						_current = (_current > _options.max)? _options.max : _options.min;
					}
					
					jQuery(this).val(_current);	
				}
				
				_oldValue = _current;
				return true;
			});

	});
}