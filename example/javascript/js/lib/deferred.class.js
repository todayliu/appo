'use strict'
define([], function() {
	function Deferred(canceller) {
		this.called = false
		this.running = false
		this.result = null
		this.pauseCount = 0
		this.callbacks = []
		this.verbose = false
		this._canceller = canceller

		// If this Deferred is cancelled and the creator of this Deferred
		// didn't cancel it, then they may not know about the cancellation and
		// try to resolve or reject it as well. This flag causes the
		// "already called" error that resolve() or reject() normally throws
		// to be suppressed once.
		this._suppressAlreadyCalled = false
	}

	if (typeof Object.defineProperty === 'function') {
		var _consumeThrownExceptions = true
		Object.defineProperty(Deferred, 'consumeThrownExceptions', {
			enumerable: false,
			set: function(v) {
				_consumeThrownExceptions = v
			},
			get: function() {
				return _consumeThrownExceptions
			}
		})
	} else {
		Deferred.consumeThrownExceptions = true
	}

	Deferred.prototype.cancel = function() {
		if (!this.called) {
			if (typeof this._canceller === 'function') {
				this._canceller(this)
			} else {
				this._suppressAlreadyCalled = true
			}
			if (!this.called) {
				this.reject('cancelled')
			}
		} else if (this.result instanceof Deferred) {
			this.result.cancel()
		}
	}

	Deferred.prototype.done = function(callback, errback) {
		this.callbacks.push({
			callback: callback,
			errback: errback
		})
		if (this.called) _run(this)
		return this
	}

	Deferred.prototype.fail = function(errback) {
		this.callbacks.push({
			callback: null,
			errback: errback
		})
		if (this.called) _run(this)
		return this
	}

	Deferred.prototype.always = function(callback) {
		return this.done(callback, callback)
	}

	Deferred.prototype.resolve = function(result) {
		_startRun(this, result)
		return this
	}

	Deferred.prototype.reject = function(err) {
		if (!(err instanceof Failure)) {
			err = new Failure(err)
		}
		_startRun(this, err)
		return this
	}

	Deferred.prototype.pause = function() {
		this.pauseCount += 1
		if (this.extra) {
			console.log('Deferred.pause ' + this.pauseCount + ': ' + this.extra)
		}
		return this
	}

	Deferred.prototype.unpause = function() {
		this.pauseCount -= 1
		if (this.extra) {
			console.log('Deferred.unpause ' + this.pauseCount + ': ' + this.extra)
		}
		if (this.pauseCount <= 0 && this.called) {
			_run(this)
		}
		return this
	}

	// For debugging
	Deferred.prototype.inspect = function(extra, cb) {
		this.extra = extra
		var self = this
		return this.done(function(r) {
			console.log('Deferred.inspect resolved: ' + self.extra)
			console.dir(r)
			return r
		}, function(e) {
			console.log('Deferred.inspect rejected: ' + self.extra)
			console.dir(e)
			return e
		})
	}

	/// A couple of sugary methods

	Deferred.prototype.thenReturn = function(result) {
		return this.done(function(_) {
			return result
		})
	}

	Deferred.prototype.thenCall = function(f) {
		return this.done(function(result) {
			f(result)
			return result
		})
	}

	Deferred.prototype.failReturn = function(result) {
		return this.fail(function(_) {
			return result
		})
	}

	Deferred.prototype.failCall = function(f) {
		return this.fail(function(result) {
			f(result)
			return result
		})
	}

	function _continue(d, newResult) {
		d.result = newResult
		d.unpause()
		return d.result
	}

	function _nest(outer) {
		outer.result.always(function(newResult) {
			return _continue(outer, newResult)
		})
	}

	function _startRun(d, result) {
		if (d.called) {
			if (d._suppressAlreadyCalled) {
				d._suppressAlreadyCalled = false
				return
			}
			throw new Error("Already resolved Deferred: " + d)
		}
		d.called = true
		d.result = result
		if (d.result instanceof Deferred) {
			d.pause()
			_nest(d)
			return
		}
		_run(d)
	}

	function _run(d) {
		if (d.running) return
		var link, status, fn
		if (d.pauseCount > 0) return
		while (d.callbacks.length > 0) {
			link = d.callbacks.shift()
			status = (d.result instanceof Failure) ? 'errback' : 'callback'
			fn = link[status]
			if (typeof fn !== 'function') continue
			try {
				d.running = true
				d.result = fn(d.result)
				d.running = false
				if (d.result instanceof Deferred) {
					d.pause()
					_nest(d)
					return
				}
			} catch (e) {
				if (Deferred.consumeThrownExceptions) {
					d.running = false
					var f = new Failure(e)
					f.source = f.source || status
					d.result = f
					if (d.verbose) {
						console.warn('uncaught error in deferred ' + status + ': ' + e.message)
						console.warn('Stack: ' + e.stack)
					}
				} else {
					throw e
				}
			}
		}
	}

	function Failure(v) {
		this.value = v
	}
	return Deferred;
})