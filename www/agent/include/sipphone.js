var WebPhone = WebPhone || {};
WebPhone = function() {
	var webphone;

	var Phone = function() {
		this.audioRemote = null;
		this.videoLocal = this.videoRemote = null;
		this.viewVideoRemote = this.viewVideoLocal = null;
		this.oSipStack = this.oSipSessionRegister = this.oSipSessionCall = this.oSipSessionTransferCall = null;
		this.oNotifICall = null;
		this.oReadyStateTimer = null;

		this.isReg = false;
		this.isIncall = false;

		this.permreq = false;

		this.enableVideo = false;
		this.enableNotifications = false;
		this.enableAutoanswer = false;
		this.hidePhone = false;
		this.registerOnStartup = false;

		this.displayName=null;
		this.privateIdentity=null;
		this.publicIdentity=null;
		this.password=null;
		this.realm=null;
		this.websocket_proxy=null;
		this.outbound_proxy=null;
		this.ice_server=null;
		this.ice_credential=null;
		this.debugLevel=null;
		this.onreg=null;
		this.onpermask=null;
		this.onpermgrant=null;
		this.onpermdeny=null;
		//this.ice_server = 'turn:osdial@10.13.5.76:3478';
		//this.ice_credential = 'osdial';

		this.webPhoneDiv=null;
		this.btnRegister=null;
		this.btnUnRegister=null;
		this.btnCall=null;
		this.btnHangUp=null;
		this.WebPhoneDTMF=null;
		this.txtRegStatus=null;
		this.txtCallStatus=null;
		this.dtmfTone=null;
		this.ringtone=null;
		this.ringbacktone=null;
		this.enteredNumber=null;
		this.divGlassPanel=null;
		this.divVideo=null;
		this.divVideoRemote=null;
		this.divVideoLocal=null;
		this.vidVideoRemote=null;
		this.vidVideoLocal=null;
		this.audAudioRemote=null;

		this.createPhone();
	}


	var onEvent = function(target, etype, func) {
		if (target.addEventListener) {
			target.addEventListener(etype, func, false);
		} else if (target.attachEvent) {
			target.attachEvent("on" + etype, func);
		} else {
			target["on" + etype] = func;
		}
	};

	onEvent(window, "load", function() {
		if (!webphone) webphone = new Phone;
	});

	Phone.prototype = {

		createPhone: function () {
			this.getPhoneParams();
			this.makePhone();
			if (this.enableVideo) {
				this.videoLocal = this.vidVideoLocal;
				this.videoRemote = this.vidVideoRemote;
			}
			this.audioRemote = this.audAudioRemote;

			SIPml.setDebugLevel(this.debugLevel);
			this.oReadyStateTimer = setInterval(function () {
				if (document.readyState === "complete") {
					clearInterval(this.oReadyStateTimer);
					// initialize SIPML5
					SIPml.init(this.postInit);
				}
			},500);
			if (this.registerOnStartup) {
				this.sipRegister();
			}
    		},

		getPhoneParams: function() {
			this.webPhoneDiv = document.getElementById('WebPhone');

			this.enableVideo = this.checkAttribTF(this.webPhoneDiv,'enableVideo', null);
			if (!this.enableVideo) this.enableVideo = this.checkAttribTF(this.webPhoneDiv,'video', false);
			this.enableNotifications = this.checkAttribTF(this.webPhoneDiv,'enableNotifications', null);
			if (!this.enableNotifications) this.enableNotifications = this.checkAttribTF(this.webPhoneDiv,'notifications', false);
			this.enableAutoanswer = this.checkAttribTF(this.webPhoneDiv,'enableAutoanswer', null);
			if (!this.enableAutoanswer) this.enableAutoanswer = this.checkAttribTF(this.webPhoneDiv,'autoAnswer', null);
			if (!this.enableAutoanswer) this.enableAutoanswer = this.checkAttribTF(this.webPhoneDiv,'autoanswer', false);
			this.hidePhone = this.checkAttribTF(this.webPhoneDiv,'hidePhone', null);
			if (!this.hidePhone) this.hidePhone = this.checkAttribTF(this.webPhoneDiv,'hide', false);
			this.registerOnStartup = this.checkAttribTF(this.webPhoneDiv,'registerOnStartup', null);
			if (!this.registerOnStartup) this.registerOnStartup = this.checkAttribTF(this.webPhoneDiv,'register', false);

			this.displayName = this.checkAttrib(this.webPhoneDiv,'displayName');
			this.privateIdentity = this.checkAttrib(this.webPhoneDiv,'privateIdentity');
			if (!this.privateIdentity) this.privateIdentity = this.checkAttrib(this.webPhoneDiv,'user');
			if (!this.privateIdentity) this.privateIdentity = this.checkAttrib(this.webPhoneDiv,'username');
			this.publicIdentity = this.checkAttrib(this.webPhoneDiv,'publicIdentity');
			this.password = this.checkAttrib(this.webPhoneDiv,'password');
			if (!this.password) this.password = this.checkAttrib(this.webPhoneDiv,'pass');
			this.realm = this.checkAttrib(this.webPhoneDiv,'realm');
			if (!this.realm) this.realm = this.checkAttrib(this.webPhoneDiv,'domain');
			if (!this.realm) this.realm = this.checkAttrib(this.webPhoneDiv,'host');
			this.websocket_proxy = this.checkAttrib(this.webPhoneDiv,'websocketProxy');
			if (!this.websocket_proxy) this.websocket_proxy = this.checkAttrib(this.webPhoneDiv,'websocket');
			this.outbound_proxy = this.checkAttrib(this.webPhoneDiv,'outboundProxy');
			if (!this.outbound_proxy) this.outbound_proxy = this.checkAttrib(this.webPhoneDiv,'outbound');
			this.ice_server = this.checkAttrib(this.webPhoneDiv,'iceServer');
			this.ice_credential = this.checkAttrib(this.webPhoneDiv,'iceCredential');
			this.onreg = this.checkAttrib(this.webPhoneDiv,'onregister');
			this.onpermask = this.checkAttrib(this.webPhoneDiv,'onpermissionask');
			this.onpermgrant = this.checkAttrib(this.webPhoneDiv,'onpermissiongranted');
			this.onpermdeny = this.checkAttrib(this.webPhoneDiv,'onpermissiondeny');

			this.debugLevel = this.checkAttrib(this.webPhoneDiv,'debugLevel');
			if (!(this.debugLevel == 'info' || this.debugLevel == 'warn' || this.debugLevel == 'error' || this.debugLevel == 'fatal')) this.debugLevel='error';

			if (this.privateIdentity) {
				if (!this.displayName) this.displayName=this.privateIdentity;
				if (this.realm) {
					if (!this.publicIdentity) this.publicIdentity = "sip:" + this.privateIdentity + "@" + this.realm;
				}
			}

		},

		checkAttrib: function(ele, attrib) {
			if (ele.getAttribute(attrib) && ele.getAttribute(attrib).length>0) return ele.getAttribute(attrib);
			return null;
		},
		checkAttribTF: function(ele, attrib, defTF) {
			if (!defTF) defTF=null;
			if (ele.getAttribute(attrib) && ele.getAttribute(attrib).length>0) {
				if (ele.getAttribute(attrib) == '0' || ele.getAttribute(attrib).substring(0,1).toUpperCase() == 'N' || ele.getAttribute(attrib).substring(0,1).toUpperCase() == 'F') {
					return false;
				} else if (ele.getAttribute(attrib) == '1' || ele.getAttribute(attrib).substring(0,1).toUpperCase() == 'Y' || ele.getAttribute(attrib).substring(0,1).toUpperCase() == 'T') {
					return true;
				}
			}
			return defTF;
		},

		postInit: function () {
			// check webrtc4all version
			if (SIPml.isWebRtc4AllSupported() && SIPml.isWebRtc4AllPluginOutdated()) {            
				if (confirm("Your WebRtc4all extension is outdated. A new version(" +SIPml.getWebRtc4AllVersion()+") with critical bug fix is available. Do you want to install it?\nIMPORTANT: You must restart your browser after the installation.")) {
					window.location = 'http://code.google.com/p/webrtc4all/downloads/list';
					return;
				}
			}

			// check for WebRTC support
			if (!SIPml.isWebRtcSupported()) {
				// is it chrome?
				if (SIPml.getNavigatorFriendlyName() == 'chrome') {
					if (confirm("You're using an old Chrome version or WebRTC is not enabled.\nDo you want to see how to enable WebRTC?")) {
						window.location = 'http://www.webrtc.org/running-the-demos';
					} else {
						window.location = "index.html";
					}
					return;
				}
                
				// for now the plugins (WebRTC4all only works on Windows)
				if (SIPml.getSystemFriendlyName() == 'windows') {
					// Internet explorer
					if (SIPml.getNavigatorFriendlyName() == 'ie') {
						// Check for IE version 
						if (parseFloat(SIPml.getNavigatorVersion()) < 9.0) {
							if (confirm("You are using an old IE version. You need at least version 9. Would you like to update IE?")) {
								window.location = 'http://windows.microsoft.com/en-us/internet-explorer/products/ie/home';
							} else {
								window.location = "index.html";
							}
						}

						// check for WebRTC4all extension
						if (!SIPml.isWebRtc4AllSupported()) {
							if (confirm("webrtc4all extension is not installed. Do you want to install it?\nIMPORTANT: You must restart your browser after the installation.")) {
								window.location = 'http://code.google.com/p/webrtc4all/downloads/list';
							} else {
								// Must do nothing: give the user the chance to accept the extension
								// window.location = "index.html";
							}
						}
						// break page loading ('window.location' won't stop JS execution)
						if (!SIPml.isWebRtc4AllSupported()) {
							return;
						}
					} else if (SIPml.getNavigatorFriendlyName() == "safari" || SIPml.getNavigatorFriendlyName() == "firefox" || SIPml.getNavigatorFriendlyName() == "opera") {
						if (confirm("Your browser don't support WebRTC.\nDo you want to install WebRTC4all extension to enjoy audio/video calls?\nIMPORTANT: You must restart your browser after the installation.")) {
							window.location = 'http://code.google.com/p/webrtc4all/downloads/list';
						} else {
							window.location = "index.html";
						}
						return;
					}

				} else {
					// OSX, Unix, Android, iOS...
					if (confirm('WebRTC not supported on your browser.\nDo you want to download a WebRTC-capable browser?')) {
						window.location = 'https://www.google.com/intl/en/chrome/browser/';
					} else {
						window.location = "index.html";
					}
					return;
				}
			}

			// checks for WebSocket support
			if (!SIPml.isWebSocketSupported() && !SIPml.isWebRtc4AllSupported()) {
				if (confirm('Your browser don\'t support WebSockets.\nDo you want to download a WebSocket-capable browser?')) {
					window.location = 'https://www.google.com/intl/en/chrome/browser/';
				} else {
					window.location = "index.html";
				}
				return;
			}

			var webphone = WebPhone.getPhone();
			if (webphone.enableVideo) {
				// attachs video displays
				if (SIPml.isWebRtc4AllSupported()) {
					webphone.viewVideoLocal = webphone.divVideoLocal;
					webphone.viewVideoRemote = webphone.divVideoRemote;
					WebRtc4all_SetDisplays(webphone.viewVideoLocal, webphone.viewVideoRemote);
				} else {
					webphone.viewVideoLocal = webphone.videoLocal;
					webphone.viewVideoRemote = webphone.videoRemote;
				}
			}

			if (!SIPml.isWebRtc4AllSupported() && !SIPml.isWebRtcSupported()) {
				if (confirm('Your browser don\'t support WebRTC.\naudio/video calls will be disabled.\nDo you want to download a WebRTC-capable browser?')) {
					window.location = 'https://www.google.com/intl/en/chrome/browser/';
				}
			}

			//document.body.style.cursor = 'default';
			//webphone.btnRegister.disabled=false;
			//webphone.btnUnRegister.disabled=true;
			//webphone.btnCall.disabled = true;
			//webphone.btnHangUp.disabled = true;
			////webphone.WebPhoneDTMF.style.display="none";
			//webphone.WebPhoneDTMF.disabled=true;
		},

		showNotifICall: function (s_number) {
			if (this.enableNotifications) {
				// permission already asked when we registered
				if (window.webkitNotifications && window.webkitNotifications.checkPermission() == 0) {
					if (this.oNotifICall) {
						oNotifICall.cancel();
					}
					this.oNotifICall = window.webkitNotifications.createNotification('images/sipml-34x39.png', 'Incoming call', 'Incoming call from ' + s_number);
					this.oNotifICall.onclose = function () { this.oNotifICall = null; };
					this.oNotifICall.show();
				}
			}
		},



		// sends SIP REGISTER request to login
		sipRegister: function () {
			this.btnRegister.disabled=true;
			this.btnUnRegister.disabled=true;
			this.btnCall.disabled = true;
			this.btnHangUp.disabled = true;
			this.WebPhoneDTMF.disabled=false;
			// catch exception for IE (DOM not ready)
			try {
				// enable notifications if not already done
				if (this.enableNotifications) {
					if (window.webkitNotifications && window.webkitNotifications.checkPermission() != 0) {
						window.webkitNotifications.requestPermission();
					}
				}

				// create SIP stack
				this.oSipStack = new SIPml.Stack({
					realm: this.realm,
					impi: this.privateIdentity,
					impu: this.publicIdentity,
					password: this.password,
					display_name: this.displayName,
					websocket_proxy_url: this.websocket_proxy,
					outbound_proxy_url: this.outbound_proxy,
					ice_servers: this.ice_server ? [{ url: this.ice_server, credential: this.ice_credential ? this.ice_credential : null }] : [],
					enable_rtcweb_breaker: false,
					enable_media_stream_cache: true,
					events_listener: { events: '*', listener: this.onSipEventStack },
					sip_headers: [
						{ name: 'User-Agent', value: 'IM-client/OMA1.0 sipML5-v1.2013.08.10' },
						{ name: 'Organization', value: 'Call Center Service Group' }
					]
				});
				if (this.oSipStack.start() != 0) {
					this.txtRegStatus.innerHTML = '<b>Failed to start the SIP stack</b>';
				} else {
					this.btnRegister.disabled=true;
					this.btnUnRegister.disabled=false;
					this.btnCall.disabled = false;
					this.btnHangUp.disabled = true;
					return;
				}
			} catch (e) {
				this.txtRegStatus.innerHTML = "<b>2:" + e + "</b>";
			}
			this.btnRegister.disabled=false;
		},


		// sends SIP REGISTER (expires=0) to logout
		sipUnRegister: function () {
			this.btnRegister.disabled=true;
			this.btnUnRegister.disabled=true;
			this.btnCall.disabled = true;
			this.btnHangUp.disabled = true;
			this.WebPhoneDTMF.disabled=true;
			this.enteredNumber.value='';
			if (this.oSipStack) {
				// shutdown all sessions
				this.oSipStack.stop();
				this.btnRegister.disabled=false;
			}
		},

		sipCall: function () {
			var oConf = {
				audio_remote: this.audioRemote,
				video_local: this.enableVideo ? this.viewVideoLocal : null,
				video_remote: this.enableVideo ? this.viewVideoRemote : null,
				events_listener: { events: '*', listener: this.onSipEventSession },
				sip_caps: [
					{ name: '+g.oma.sip-im' },
					{ name: '+sip.ice' },
					{ name: 'language', value: '\"en\"' }
				]
        		};
			if (this.oSipStack && !this.oSipSessionCall && this.enteredNumber.value!='') {
				this.btnCall.disabled = true;
				this.btnHangUp.disabled = false;
				this.oSipSessionCall = this.oSipStack.newSession(this.enableVideo ? 'call-audiovideo' : 'call-audio',oConf);
				if (this.oSipSessionCall.call(this.enteredNumber.value) != 0) {
					this.oSipSessionCall=null;
					this.txtCallStatus.innerHTML = 'Failed to make call';
					this.btnCall.disabled = false;
					this.btnHangUp.disabled = true;
					return;
				}

			} else if (this.oSipSessionCall) {
				this.txtCallStatus.innerHTML = '<i>Connecting...</i>';
				this.oSipSessionCall.accept(oConf);
			}
		},


		// terminates the call (SIP BYE or CANCEL)
		sipHangUp: function () {
			if (this.oSipSessionCall) {
				this.txtCallStatus.innerHTML = '<i>Terminating the call...</i>';
				this.oSipSessionCall.hangup({events_listener: { events: '*', listener: this.onSipEventSession }});
			}
		},


		sipSendDTMF: function (c){
			if (!this.WebPhoneDTMF.disabled) {;
				var tc = c;
				if (c) {
					if (c=="*") c="star";
					if (c=="#") c="hash";
					if (this.oSipSessionCall) {
						if (this.oSipSessionCall.dtmf(c) == 0) {
							try {
								this.dtmfTone.src='/osdial/agent/sounds/dtmf-'+c+'.wav';;
								this.dtmfTone.play();
							} catch(e) { }
						}
					} else {
						try {
							this.dtmfTone.src='/osdial/agent/sounds/dtmf-'+c+'.wav';;
							this.dtmfTone.play();
						} catch(e) { }
						this.enteredNumber.value += c;
					}
				}
			}
		},

		startRingTone: function () {
			try { this.ringtone.play(); } catch (e) { }
		},

		stopRingTone: function () {
			try { this.ringtone.pause(); } catch (e) { }
		},

		startRingbackTone: function () {
			try { this.ringbacktone.play(); } catch (e) { }
		},

		stopRingbackTone: function () {
			try { this.ringbacktone.pause(); } catch (e) { }
		},


		uiCallTerminated: function (desc) {
			this.btnCall.disabled = false;
			this.btnHangUp.disabled = true;
			//WebPhoneDTMF.style.display="none";
			this.WebPhoneDTMF.disabled=false;
			this.oSipSessionCall = null;
			this.stopRingbackTone();
			this.stopRingTone();
			this.txtCallStatus.innerHTML = '<i>'+desc+'</i>';
			if (this.enableNotifications) {
				if (this.oNotifICall) {
		    			this.oNotifICall.cancel();
		    			this.oNotifICall = null;
				}
			}
			setTimeout(function () { if (!this.oSipSessionCall) this.txtCallStatus.innerHTML = ''; }, 2500);
		},

		uiOnConnectionEvent: function (b_connected, b_connecting) {
			this.btnRegister.disabled = b_connected || b_connecting;
			this.btnUnRegister.disabled = !b_connected && !b_connecting;
			this.btnCall.disabled = !(b_connected && tsk_utils_have_webrtc() && tsk_utils_have_stream());
			this.btnHangUp.disabled = !this.oSipSessionCall;
			if (b_connected && !this.oSipSessionCall) {
				if (this.onreg) {
					var myonreg=new Function (this.onreg);
					myonreg();
				}
			}
			this.isReg = !this.btnRegister.disabled;
			this.isInCall = !this.btnCall.disabled;
		},


		onSipEventStack: function (e) {
			//tsk_utils_log_info('==stack event = ' + e.type);
			var webphone = WebPhone.getPhone();
			//var webphone = document.getElementById('WebPhone').phone;
			switch (e.type) {
				case 'started':
				{
					// catch exception for IE (DOM not ready)
					try {
						// LogIn (REGISTER) as soon as the stack finish starting
						webphone.oSipSessionRegister = this.newSession('register', {
							expires: 60,
							events_listener: { events: '*', listener: webphone.onSipEventSession },
							sip_caps: [
								{ name: '+g.oma.sip-im', value: null },
								{ name: '+audio', value: null },
								{ name: 'language', value: '\"en\"' }
							]
						});
						webphone.oSipSessionRegister.register();
					} catch (e) {
						webphone.txtRegStatus.innerHTML = webphone.txtRegStatus.innerHTML = "<b>1:" + e + "</b>";
					}
					break;
				}

				case 'stopping': case 'stopped': case 'failed_to_start': case 'failed_to_stop':
				{
					var bFailure = (e.type == 'failed_to_start') || (e.type == 'failed_to_stop');
					webphone.oSipStack = null;
					webphone.oSipSessionRegister = null;
					webphone.oSipSessionCall = null;
					webphone.uiOnConnectionEvent(false, false);
					webphone.stopRingbackTone();
					webphone.stopRingTone();
					webphone.txtCallStatus.innerHTML = '';
					webphone.txtRegStatus.innerHTML = bFailure ? "<i>Disconnected: <b>" + e.description + "</b></i>" : "<i>Disconnected</i>";
					break;
				}

				case 'i_new_message':
				{
					e.newSession.accept();
					console.info('SMS-content = ' + e.getContentString() + ' and SMS-content-type = ' + e.getContentType());
				}

				case 'i_new_call':
				{
					if (webphone.oSipSessionCall) {
						// Multi-line here.
						// do not accept the incoming call if we're already 'in call'
						e.newSession.hangup();
					} else {
						webphone.oSipSessionCall = e.newSession;
						webphone.btnCall.disabled = false;
						webphone.btnHangUp.disabled = false;
						if (webphone.enableAutoanswer) {
							webphone.sipCall();
						} else {
							webphone.startRingTone();
							var sRemoteNumber = (webphone.oSipSessionCall.getRemoteFriendlyName() || 'unknown');
							webphone.txtCallStatus.innerHTML = "<i>Incoming call from [<b>" + sRemoteNumber + "</b>]</i>";
							if (webphone.enableNotifications) {
								webphone.showNotifICall(sRemoteNumber);
							}
						}
					}
					break;
				}

				case 'm_permission_requested':
				{
					webphone.divGlassPanel.style.visibility = 'visible';
					if (webphone.onpermask) {
						var myonpermask=new Function (webphone.onpermask);
						myonpermask();
					}
					webphone.permreq=true;
					break;
				}

				case 'm_permission_accepted': case 'm_permission_refused':
				{
					webphone.divGlassPanel.style.visibility = 'hidden';
					if (e.type == 'm_permission_refused') {
						webphone.uiCallTerminated('Media stream permission denied');
						if (webphone.permreq) {
							if (webphone.onpermdeny) {
								var myonpermdeny=new Function (webphone.onpermdeny);
								myonpermdeny();
							}
						}
					} else {
						if (webphone.onpermgrant) {
							var myonpermgrant=new Function (webphone.onpermgrant);
							myonpermgrant();
						}
					}
					break;
				}

				case 'starting': default: break;
			}
		},


		// Callback function for SIP sessions (INVITE, REGISTER, MESSAGE...)
		onSipEventSession: function (e) {
			//tsk_utils_log_info('==session event = ' + e.type);
			var webphone = WebPhone.getPhone();
			//var webphone = document.getElementById('WebPhone').phone;
			switch (e.type) {
				case 'connecting': case 'connected':
				{
					var bConnected = (e.type == 'connected');
					if (e.session == webphone.oSipSessionRegister) {
						webphone.uiOnConnectionEvent(bConnected, !bConnected);
						webphone.txtRegStatus.innerHTML = "<i>" + e.description + "</i>";
					} else if (e.session == webphone.oSipSessionCall) {
						//WebPhoneDTMF.style.display="inline-block";
						webphone.WebPhoneDTMF.disabled=false;
						webphone.btnCall.disabled = true;
						webphone.btnHangUp.disabled = false;
						if (bConnected) {
							webphone.stopRingbackTone();
							webphone.stopRingTone();
							if (webphone.enableNotifications) {
								if (webphone.oNotifICall) {
							    		webphone.oNotifICall.cancel();
							    		webphone.oNotifICall = null;
								}
							}
						}
						webphone.txtCallStatus.innerHTML = "<i>" + e.description + "</i>";
					}
					break;
				}

				case 'terminating': case 'terminated':
				{
					if (e.session == webphone.oSipSessionRegister) {
						webphone.uiOnConnectionEvent(false, false);
						webphone.oSipSessionCall = null;
						webphone.oSipSessionRegister = null;
						webphone.txtRegStatus.innerHTML = "<i>" + e.description + "</i>";
					} else if (e.session == webphone.oSipSessionCall) {
						webphone.uiCallTerminated(e.description);
					}
					break;
				}

				case 'm_stream_video_local_added': case 'm_stream_video_local_removed': case 'm_stream_video_remote_added': case 'm_stream_video_remote_removed':
				{
					if (webphone.enableVideo) {
						if (e.session == webphone.oSipSessionCall) { }
					}
					break;
				}

				case 'm_stream_audio_local_added': case 'm_stream_audio_local_removed': case 'm_stream_audio_remote_added': case 'm_stream_audio_remote_removed':
				{
					break;
				}

				case 'i_ect_new_call':
				{
					webphone.oSipSessionTransferCall = e.session;
					break;
				}
	
				case 'i_ao_request':
				{
					if (e.session == webphone.oSipSessionCall) {
						var iSipResponseCode = e.getSipResponseCode();
						if (iSipResponseCode == 180 || iSipResponseCode == 183) {
							webphone.startRingbackTone();
							webphone.txtCallStatus.innerHTML = '<i>Remote ringing...</i>';
						}
					}
					break;
				}

				case 'm_early_media':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.stopRingbackTone();
						webphone.stopRingTone();
						webphone.txtCallStatus.innerHTML = '<i>Early media started</i>';
					}
					break;
				}

				case 'm_local_hold_ok':
				{
					if (e.session == webphone.oSipSessionCall) {
						if (webphone.oSipSessionCall.bTransfering) {
							webphone.oSipSessionCall.bTransfering = false;
							// this.AVSession.TransferCall(this.transferUri);
						}
						webphone.txtCallStatus.innerHTML = '<i>Call placed on hold</i>';
						webphone.oSipSessionCall.bHeld = true;
					}
					break;
				}

				case 'm_local_hold_nok':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.oSipSessionCall.bTransfering = false;
						webphone.txtCallStatus.innerHTML = '<i>Failed to place remote party on hold</i>';
					}
					break;
				}

				case 'm_local_resume_ok':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.oSipSessionCall.bTransfering = false;
						webphone.txtCallStatus.innerHTML = '<i>Call taken off hold</i>';
						webphone.oSipSessionCall.bHeld = false;
					}
					break;
				}

				case 'm_local_resume_nok':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.oSipSessionCall.bTransfering = false;
						webphone.txtCallStatus.innerHTML = '<i>Failed to unhold call</i>';
					}
					break;
				}

				case 'm_remote_hold':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.txtCallStatus.innerHTML = '<i>Placed on hold by remote party</i>';
					}
					break;
				}

				case 'm_remote_resume':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.txtCallStatus.innerHTML = '<i>Taken off hold by remote party</i>';
					}
					break;
				}

				case 'o_ect_trying':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.txtCallStatus.innerHTML = '<i>Call transfer in progress...</i>';
					}
					break;
				}

				case 'o_ect_accepted':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.txtCallStatus.innerHTML = '<i>Call transfer accepted</i>';
					}
					break;
				}

				case 'o_ect_completed': case 'i_ect_completed':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.txtCallStatus.innerHTML = '<i>Call transfer completed</i>';
						if (webphone.oSipSessionTransferCall) {
							webphone.oSipSessionCall = webphone.oSipSessionTransferCall;
						}
						webphone.oSipSessionTransferCall = null;
					}
					break;
				}

				case 'o_ect_failed': case 'i_ect_failed':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.txtCallStatus.innerHTML = '<i>Call transfer failed</i>';
					}
					break;
				}

				case 'o_ect_notify': case 'i_ect_notify':
				{
					if (e.session == webphone.oSipSessionCall) {
						webphone.txtCallStatus.innerHTML = "<i>Call Transfer: <b>" + e.getSipResponseCode() + " " + e.description + "</b></i>";
						if (e.getSipResponseCode() >= 300) {
							if (webphone.oSipSessionCall.bHeld) {
								webphone.oSipSessionCall.resume();
							}
						}
					}
					break;
				}

				case 'i_ect_requested':
				{
					if (e.session == webphone.oSipSessionCall) {
						var s_message = "Do you accept call transfer to [" + e.getTransferDestinationFriendlyName() + "]?";//FIXME
						if (confirm(s_message)) {
							webphone.txtCallStatus.innerHTML = "<i>Call transfer in progress...</i>";
							webphone.oSipSessionCall.acceptTransfer();
							break;
						}
						webphone.oSipSessionCall.rejectTransfer();
					}
					break;
				}

			}
		},

		makePhone: function () {
			var me = this;

			if (this.hidePhone) {
				this.webPhoneDiv.style.visibility = 'hidden';
				//this.webPhoneDiv.style.width = '1px';
				//this.webPhoneDiv.style.height = '1px';
			}

			var wpcontrols = document.createElement('div');
			wpcontrols.id = 'WebPhoneControls';
			/*if (this.hidePhone) {
				wpcontrols.style.width = '1px';
				wpcontrols.style.height = '1px';
			}*/
			this.webPhoneDiv.appendChild(wpcontrols);


			this.btnRegister = document.createElement('input');
			this.btnRegister.type = 'button';
			this.btnRegister.id = 'btnRegister';
			this.btnRegister.className = 'WebPhoneButton';
			this.btnRegister.value = 'Login';
			this.btnRegister.onclick = function() { WebPhone.getPhone().sipRegister(); };
			wpcontrols.appendChild(this.btnRegister);

			var wpspan1 = document.createElement('span');
			wpspan1.innerHTML = '&nbsp;';
			wpcontrols.appendChild(wpspan1);

			this.btnUnRegister = document.createElement('input');
			this.btnUnRegister.type = 'button';
			this.btnUnRegister.id = 'btnUnRegister';
			this.btnUnRegister.className = 'WebPhoneButton';
			this.btnUnRegister.value = 'Logout';
			this.btnUnRegister.disabled = 'true';
			this.btnUnRegister.onclick = function() { WebPhone.getPhone().sipUnRegister(); };
			wpcontrols.appendChild(this.btnUnRegister);


			var wpdblclick = function() {
				if (WebPhone.getPhone().WebPhoneDTMF.style.display=='none') {
					WebPhone.getPhone().WebPhoneDTMF.style.display='inline-block';
				} else {
					WebPhone.getPhone().WebPhoneDTMF.style.display='none';
				}
			};
			var wpscrn = document.createElement('div');
			wpscrn.id = 'WebPhoneScreen';
			wpscrn.ondblclick = wpdblclick;
			/*if (this.hidePhone) {
				wpscrn.style.width = '1px';
				wpscrn.style.height = '1px';
			}*/
			wpcontrols.appendChild(wpscrn);

			this.txtRegStatus = document.createElement('label');
			this.txtRegStatus.id = 'txtRegStatus';
			wpscrn.appendChild(this.txtRegStatus);

			var wpsbr1 = document.createElement('br');
			wpscrn.appendChild(wpsbr1);

			var wpsbr2 = document.createElement('br');
			wpscrn.appendChild(wpsbr2);

			this.txtCallStatus = document.createElement('label');
			this.txtCallStatus.id = 'txtCallStatus';
			wpscrn.appendChild(this.txtCallStatus);

			var wpsbr3 = document.createElement('br');
			wpscrn.appendChild(wpsbr3);

			this.enteredNumber = document.createElement('input');
			this.enteredNumber.id = 'enteredNumber';
			this.enteredNumber.name = 'enteredNumber';
			this.enteredNumber.size = '13';
			this.enteredNumber.type = 'text';
			wpscrn.appendChild(this.enteredNumber);


			this.btnHangUp = document.createElement('input');
			this.btnHangUp.type = 'button';
			this.btnHangUp.id = 'btnHangUp';
			this.btnHangUp.className = 'WebPhoneButton';
			this.btnHangUp.value = 'Hangup';
			this.btnHangUp.disabled = 'true';
			this.btnHangUp.onclick = function() { WebPhone.getPhone().sipHangUp(); };
			wpcontrols.appendChild(this.btnHangUp);

			var wpspan2 = document.createElement('span');
			wpspan2.innerHTML = '&nbsp;';
			wpcontrols.appendChild(wpspan2);

			this.btnCall = document.createElement('input');
			this.btnCall.type = 'button';
			this.btnCall.id = 'btnCall';
			this.btnCall.className = 'WebPhoneButton';
			this.btnCall.value = 'Call';
			this.btnCall.disabled = 'true';
			this.btnCall.onclick = function() { WebPhone.getPhone().sipCall(); };
			wpcontrols.appendChild(this.btnCall);

			var wpbr = document.createElement('br');
			wpcontrols.appendChild(wpbr);



			this.WebPhoneDTMF = document.createElement('div');
			this.WebPhoneDTMF.id = 'WebPhoneDTMF';
			this.WebPhoneDTMF.disabled = 'true';
			/*if (this.hidePhone) {
				this.WebPhoneDTMF.style.width = '1px';
				this.WebPhoneDTMF.style.height = '1px';
			}*/
			wpcontrols.appendChild(this.WebPhoneDTMF);


			var wpdtmftable = document.createElement('table');
			wpdtmftable.cellPadding = '0';
			wpdtmftable.align = 'center';
			wpdtmftable.width = '100';
			wpdtmftable.height = '120';
			wpdtmftable.bgColor = 'gray';
			this.WebPhoneDTMF.appendChild(wpdtmftable);


			var dtmfclick = function() { WebPhone.getPhone().sipSendDTMF(this.value); };

			var dtmfrow1 = document.createElement('tr');
			dtmfrow1.vAlign = 'middle';
			wpdtmftable.appendChild(dtmfrow1);

			var dtmfr1c1 = document.createElement('td');
			dtmfr1c1.bgColor = 'lightgray';
			dtmfr1c1.align = 'center';
			dtmfrow1.appendChild(dtmfr1c1);

			var dtmfr1c1b1 = document.createElement('input');
			dtmfr1c1b1.id = 'WebPhoneDTMF1';
			dtmfr1c1b1.type = 'button';
			dtmfr1c1b1.value = '1';
			dtmfr1c1b1.onclick = dtmfclick;
			dtmfr1c1.appendChild(dtmfr1c1b1);


			var dtmfr1c2 = document.createElement('td');
			dtmfr1c2.bgColor = 'lightgray';
			dtmfr1c2.align = 'center';
			dtmfrow1.appendChild(dtmfr1c2);

			var dtmfr1c2b2 = document.createElement('input');
			dtmfr1c2b2.id = 'WebPhoneDTMF2';
			dtmfr1c2b2.type = 'button';
			dtmfr1c2b2.value = '2';
			dtmfr1c2b2.onclick = dtmfclick;
			dtmfr1c2.appendChild(dtmfr1c2b2);

			var dtmfr1c3 = document.createElement('td');
			dtmfr1c3.bgColor = 'lightgray';
			dtmfr1c3.align = 'center';
			dtmfrow1.appendChild(dtmfr1c3);

			var dtmfr1c3b3 = document.createElement('input');
			dtmfr1c3b3.id = 'WebPhoneDTMF3';
			dtmfr1c3b3.type = 'button';
			dtmfr1c3b3.value = '3';
			dtmfr1c3b3.onclick = dtmfclick;
			dtmfr1c3.appendChild(dtmfr1c3b3);


			var dtmfrow2 = document.createElement('tr');
			dtmfrow2.vAlign = 'middle';
			wpdtmftable.appendChild(dtmfrow2);

			var dtmfr2c1 = document.createElement('td');
			dtmfr2c1.bgColor = 'lightgray';
			dtmfr2c1.align = 'center';
			dtmfrow2.appendChild(dtmfr2c1);

			var dtmfr2c1b1 = document.createElement('input');
			dtmfr2c1b1.id = 'WebPhoneDTMF4';
			dtmfr2c1b1.type = 'button';
			dtmfr2c1b1.value = '4';
			dtmfr2c1b1.onclick = dtmfclick;
			dtmfr2c1.appendChild(dtmfr2c1b1);

			var dtmfr2c2 = document.createElement('td');
			dtmfr2c2.bgColor = 'lightgray';
			dtmfr2c2.align = 'center';
			dtmfrow2.appendChild(dtmfr2c2);

			var dtmfr2c2b2 = document.createElement('input');
			dtmfr2c2b2.id = 'WebPhoneDTMF5';
			dtmfr2c2b2.type = 'button';
			dtmfr2c2b2.value = '5';
			dtmfr2c2b2.onclick = dtmfclick;
			dtmfr2c2.appendChild(dtmfr2c2b2);

			var dtmfr2c3 = document.createElement('td');
			dtmfr2c3.bgColor = 'lightgray';
			dtmfr2c3.align = 'center';
			dtmfrow2.appendChild(dtmfr2c3);

			var dtmfr2c3b3 = document.createElement('input');
			dtmfr2c3b3.id = 'WebPhoneDTMF6';
			dtmfr2c3b3.type = 'button';
			dtmfr2c3b3.value = '6';
			dtmfr2c3b3.onclick = dtmfclick;
			dtmfr2c3.appendChild(dtmfr2c3b3);


			var dtmfrow3 = document.createElement('tr');
			dtmfrow3.vAlign = 'middle';
			wpdtmftable.appendChild(dtmfrow3);

			var dtmfr3c1 = document.createElement('td');
			dtmfr3c1.bgColor = 'lightgray';
			dtmfr3c1.align = 'center';
			dtmfrow3.appendChild(dtmfr3c1);

			var dtmfr3c1b1 = document.createElement('input');
			dtmfr3c1b1.id = 'WebPhoneDTMF7';
			dtmfr3c1b1.type = 'button';
			dtmfr3c1b1.value = '7';
			dtmfr3c1b1.onclick = dtmfclick;
			dtmfr3c1.appendChild(dtmfr3c1b1);

			var dtmfr3c2 = document.createElement('td');
			dtmfr3c2.bgColor = 'lightgray';
			dtmfr3c2.align = 'center';
			dtmfrow3.appendChild(dtmfr3c2);

			var dtmfr3c2b2 = document.createElement('input');
			dtmfr3c2b2.id = 'WebPhoneDTMF8';
			dtmfr3c2b2.type = 'button';
			dtmfr3c2b2.value = '8';
			dtmfr3c2b2.onclick = dtmfclick;
			dtmfr3c2.appendChild(dtmfr3c2b2);

			var dtmfr3c3 = document.createElement('td');
			dtmfr3c3.bgColor = 'lightgray';
			dtmfr3c3.align = 'center';
			dtmfrow3.appendChild(dtmfr3c3);

			var dtmfr3c3b3 = document.createElement('input');
			dtmfr3c3b3.id = 'WebPhoneDTMF9';
			dtmfr3c3b3.type = 'button';
			dtmfr3c3b3.value = '9';
			dtmfr3c3b3.onclick = dtmfclick;
			dtmfr3c3.appendChild(dtmfr3c3b3);


			var dtmfrow4 = document.createElement('tr');
			dtmfrow4.vAlign = 'middle';
			wpdtmftable.appendChild(dtmfrow4);

			var dtmfr4c1 = document.createElement('td');
			dtmfr4c1.bgColor = 'lightgray';
			dtmfr4c1.align = 'center';
			dtmfrow4.appendChild(dtmfr4c1);

			var dtmfr4c1b1 = document.createElement('input');
			dtmfr4c1b1.id = 'WebPhoneDTMFS';
			dtmfr4c1b1.type = 'button';
			dtmfr4c1b1.value = '*';
			dtmfr4c1b1.onclick = dtmfclick;
			dtmfr4c1.appendChild(dtmfr4c1b1);

			var dtmfr4c2 = document.createElement('td');
			dtmfr4c2.bgColor = 'lightgray';
			dtmfr4c2.align = 'center';
			dtmfrow4.appendChild(dtmfr4c2);

			var dtmfr4c2b2 = document.createElement('input');
			dtmfr4c2b2.id = 'WebPhoneDTMF0';
			dtmfr4c2b2.type = 'button';
			dtmfr4c2b2.value = '0';
			dtmfr4c2b2.onclick = dtmfclick;
			dtmfr4c2.appendChild(dtmfr4c2b2);

			var dtmfr4c3 = document.createElement('td');
			dtmfr4c3.bgColor = 'lightgray';
			dtmfr4c3.align = 'center';
			dtmfrow4.appendChild(dtmfr4c3);

			var dtmfr4c3b3 = document.createElement('input');
			dtmfr4c3b3.id = 'WebPhoneDTMFP';
			dtmfr4c3b3.type = 'button';
			dtmfr4c3b3.value = '#';
			dtmfr4c3b3.onclick = dtmfclick;
			dtmfr4c3.appendChild(dtmfr4c3b3);



			var wpint = document.createElement('div');
			wpint.id = 'WebPhoneInternals';
			this.webPhoneDiv.appendChild(wpint);

			this.divGlassPanel = document.createElement('div');
			this.divGlassPanel.id = 'divGlassPanel';
			wpint.appendChild(this.divGlassPanel);

			if (this.enableVideo) {
				this.divVideo = document.createElement('div');
				this.divVideo.id = 'divVideo';
				wpint.appendChild(this.divVideo);

				this.divVideoRemote = document.createElement('div');
				this.divVideoRemote.id = 'divVideoRemote';
				this.divVideo.appendChild(this.divVideoRemote);

				this.vidVideoRemote = document.createElement('video');
				this.vidVideoRemote.id = 'video_remote';
				this.vidVideoRemote.autoplay = 'autoplay';
				this.divVideoRemote.appendChild(this.vidVideoRemote);

				this.divVideoLocal = document.createElement('div');
				this.divVideoLocal.id = 'divVideoLocal';
				this.divVideo.appendChild(this.divVideoLocal);

				this.vidVideoLocal = document.createElement('video');
				this.vidVideoLocal.id = 'video_local';
				this.vidVideoLocal.autoplay = 'autoplay';
				this.vidVideoLocal.muted = 'true';
				this.divVideoLocal.appendChild(this.vidVideoLocal);
			}

			var wpobj1 = document.createElement('object');
			wpobj1.id = 'fakeVideoDisplay';
			wpobj1.setAttribute('classid','clsid:5C2C407B-09D9-449B-BB83-C39B7802A684');
			wpobj1.className = 'hiddenObject';
			wpint.appendChild(wpobj1);

			var wpobj2 = document.createElement('object');
			wpobj2.id = 'fakeLooper';
			wpobj2.setAttribute('classid','clsid:7082C446-54A8-4280-A18D-54143846211A');
			wpobj2.className = 'hiddenObject';
			wpint.appendChild(wpobj2);

			var wpobj3 = document.createElement('object');
			wpobj3.id = 'fakeSessionDescription';
			wpobj3.setAttribute('classid','clsid:DBA9F8E2-F9FB-47CF-8797-986A69A1CA9C');
			wpobj3.className = 'hiddenObject';
			wpint.appendChild(wpobj3);

			var wpobj4 = document.createElement('object');
			wpobj4.id = 'fakeNetTransport';
			wpobj4.setAttribute('classid','clsid:5A7D84EC-382C-4844-AB3A-9825DBE30DAE');
			wpobj4.className = 'hiddenObject';
			wpint.appendChild(wpobj4);

			var wpobj5 = document.createElement('object');
			wpobj5.id = 'fakePeerConnection';
			wpobj5.setAttribute('classid','clsid:56D10AD3-8F52-4AA4-854B-41F4D6F9CEA3');
			wpobj5.className = 'hiddenObject';
			wpint.appendChild(wpobj5);

			this.audAudioRemote = document.createElement('audio');
			this.audAudioRemote.id = 'audio_remote';
			this.audAudioRemote.autoplay = 'autoplay';
			wpint.appendChild(this.audAudioRemote);

			this.ringtone = document.createElement('audio');
			this.ringtone.id = 'ringtone';
			this.ringtone.src = '/osdial/agent/sounds/ringtone.wav';
			this.ringtone.loop = 'loop';
			wpint.appendChild(this.ringtone);

			this.ringbacktone = document.createElement('audio');
			this.ringbacktone.id = 'ringbacktone';
			this.ringbacktone.src = '/osdial/agent/sounds/ringbacktone.wav';
			this.ringbacktone.loop = 'loop';
			wpint.appendChild(this.ringbacktone);

			this.dtmfTone = document.createElement('audio');
			this.dtmfTone.id = 'dtmfTone';
			this.dtmfTone.src = '/osdial/agent/sounds/dtmf.wav';
			wpint.appendChild(this.dtmfTone);

		}

	};
	// JQuery to make the WebPhone quick and draggable.
	//$(function() { $( "#WebPhone" ).draggable(); });

	return {
		reload: function() {
			console.log('reload');
		},
		getPhone: function() {
			if (webphone) return webphone;
		},
		hidePhone: function() {
			if (webphone) webphone.webPhoneDiv.style.visibility = 'hidden';
		},
		showPhone: function() {
			if (webphone) webphone.webPhoneDiv.style.visibility = 'visible';
		},
		isReg: function() {
			if (webphone) return webphone.isReg;
		},
		isIncall: function() {
			if (webphone) return webphone.isIncall;
		},
		close: function() {
			if (webphone) {
				if (webphone.oSipStack) {
					webphone.oSipStack.stop();
				} else {
					if (webphone.isIncall) {
						webphone.sipHangUp();
					}
					if (webphone.isReg) {
						webphone.sipUnRegister();
					}
				}
			}
		},
		getWebPhoneStyles: function() {
			var result = "<style>\n";
			result += "div#WebPhone {position:relative;top:-400px;left:800px;display:inline-block;background-color:#929292;width:150px;padding:5px;margin:5px;z-index:9995;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;font-family:Arial;font-size:13px;}\n";
			result += "div#WebPhone div#WebPhoneControls {display:block;text-align:center;padding-top:3px;padding-bottom:3px;background-color:#ACACAC;}\n";
			result += "div#WebPhone div#WebPhoneControls div#WebPhoneScreen {display:inline-block;text-align:center;width:80%;paddind-top:2px;padding-left:1px;padding-right:1px;padding:1px;margin:2px;border:1px dotted #DDDDDD;background-color:#4c4e52;color:white;}\n";
			result += "div#WebPhone div#WebPhoneControls div#WebPhoneScreen input#enteredNumber {width:100%;margin-right:2px;border-bottom:0px;border-left:0px;border-right:0px;border-top:1px dotted white;font-family:inherit;font-size:inherit;background-color:inherit;margin:0px;color:inherit;text-align:right;font-weight:bold;}\n";
			result += "div#WebPhone div#WebPhoneControls input.WebPhoneButton {width:58px;margin:initial;padding:initial;border:initial;}\n";
			result += "div#WebPhone div#WebPhoneControls div#WebPhoneDTMF {display:inline-block;text-align:center;}\n";
			result += "div#WebPhone div#WebPhoneControls div#WebPhoneDTMF input {width:100%;height:100%;border:initial;margin:initial;padding:initial;}\n";
			result += "div#WebPhone div#WebPhoneInternals {display:inline-block;height:1px;width:100%;}\n";
			result += "div#WebPhone div#WebPhoneInternals div#divGlassPanel {position:fixed;top:0;left:0;width:100%;height:100%;margin:0;padding:0;background-color:gray;opacity:0.8;visibility:hidden;z-index:32000;}\n";
			result += "div#WebPhone div#WebPhoneInternals div#divVideo div#divVideoRemote video {opacity:0;background-color:#000000;-webkit-transition-property:opacity;-webkit-transition-duration:2s;width:100%;height:100%;}\n";
			result += "div#WebPhone div#WebPhoneInternals div#divVideo div#divVideoLocal video {opacity:0;margin-top:-80px;margin-left:5px;background-color:#000000;-webkit-transition-property:opacity;-webkit-transition-duration:2s;width:88px;height:72px;}\n";
			result += "div#WebPhone div#WebPhoneInternals .hiddenObject {visibility:hidden;height:1px;width:1px;}\n";
			result += "div#WebPhone div#WebPhoneInternals .audioObject {height:1px;width:1px;}\n";
			result += "</style>\n";
			return result;
		}
	}
} ();
