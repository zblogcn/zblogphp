/**
 * Z-BlogPHP Clinic js class
 * @package clinic
 * @subpackage clinic.js
 */
(function(){ 
	var Clinic = { 
		_domMsg: null, 
		_aryPreInit: [],
		_tasks: [],
		_timers: [],
		_module: '',
		_taskpos: 0,
		_timerid: 0,
		
		/**
		 * Set message output DOM
		 * @param domid DOM's id
		 * @return this
		 */
		setMsgDom: function(domid) {
			this._domMsg = $(domid);
			return this;
		}, 
		
		/**
		 * Set clinic module 
		 * @param module module's id
		 * @return this
		 */
		setModuleId: function(moduleid) {
			this._module = moduleid;
			return this;
		},
		
		/**
		 * Add code to function init
		 * @param func code
		 * @return this
		 */
		preInit: function(func) {
			this._aryPreInit.push(func);
			return this;
		},
		
		/**
		 * Initialize
		 * @return this
		 */
		init: function() {
			var that = this;
			$.each(that._aryPreInit, function(index, value) {
				value();
			});
			return this;
		},
		
		/**
		 * Output message to DOM
		 * @param object message 
		 * @return this
		 */
		msg: function(object) {
			$("<tr><td>" + object + "</td></tr>").prependTo(this._domMsg);
			return this;
		}, 
		
		/**
		 * Add code to setInterval
		 * @param func code 
		 * @return this
		 */
		addTimer: function(func) {
			this._timers.push(func);
			return this;
		},	
		
		/**
		 * setInterval main code
		 * @return this
		 */
		runTimer: function() {
			var that = this;
			$.each(that._timers, function(index, value) {
				value();
			});
			return this;
		},		
	
		/**
		 * Add task
		 * @param argument[0] task code
		 * @param argument[*] arguments to the function
		 * @return this
		 */
		addTask: function(func) {
			this._tasks.push(arguments);
			return this;
		},
		
		/**
		 * Run task
		 * @param callback callback
		 * @return this
		 */
		runTask: function(callback) {
			var that = this,
				taskLength = this._tasks.length - 1,
				index = this._taskpos;
			
			
			// Set timer
			if (this._timerid == 0) {
				this._timerid = setInterval(this.runTimer, 1000); 
				console.log(this._timerid);
			}
			
			// Clear timer and exit
			if (index > taskLength) {
				clearInterval(this._timerid);
				this._timerid = 0;
				if (typeof(callback) != 'undefined') callback(index);
				return true;
			}
			else {
				console.log("执行任务" + index + "/" + taskLength + "..");
				console.log(this._tasks[index]);
				//Run task
				this._tasks[index][0](this._tasks[index]);
				if (typeof(callback) != 'undefined') callback(index);
				console.log("执行任务" + index + "/" + taskLength + "完成");
				this._taskpos++;
				return true;
			};
			
		},
		
		/**
		 * AJAX Post 
		 * @param param param
		 * @param succcallback callback when success
		 * @param errcallback callback when error
		 * @return this
		 */
		ajax: function(param, succcallback, errcallback) {
			var url = 'ajax.php';
				url += '?module=' + this._module;
			
			var ajax_option = {
				data: param,
				type: "POST",
				success: succcallback,
				error: errcallback
			}
			
			$.ajax(url, ajax_option);
			
			return this;
		}
		
	};
	
	var clinic = Object.create(Clinic).preInit(function() {
		clinic.setMsgDom('#tbody-message');
	});
	window.clinic = clinic;
})();