/* Validate.js, version 1.0.2
*  (c) 2006 achraf bouyakhsass <mutation[at]mutationevent.com>
* 
*  This software is licensed under the CC-GNU GPL
*  http://creativecommons.org/licenses/GPL/2.0/
*
*  For more details
*  http://www.mutationevent.com/project/validate.js
*
*  Package to validate various data :
*  hasValidChars
*  isSimpleIP
*  isAlphaLatin
*  isNotEmpty
*  isIntegerInRange
*  isNum
*  isEMailAddr
*  isZipCode
*  isDate
*  isMD5
*  isURL
*  isGuid
*  isISBN
*  isSSN
*  isDecimal
*  isplatform
*  addRules
*  Apply
/*--------------------------------------------------------------------------*/
var Class = {
	create: function() {
		return function() {
			this.initialize.apply(this, arguments);
		}
	}
}

function getValue(s){
if ($(s)==null) return null;
else return $(s).value;
}

var Validate = Class.create();
Validate.prototype = {
	/*--------------------------------------------------------------------------*/
	initialize:function(){
		this.error_array = []
		this.rules_array = [];
		this.e = true;
	},
	/*--------------------------------------------------------------------------*/
	hasValidChars:function(s, characters, caseSensitive){
		function escapeSpecials(s){
			return s.replace(new RegExp("([\\\\-])", "g"), "\\$1");
		}
		return new RegExp("^[" + escapeSpecials(characters) + "]+$",(!caseSensitive ? "i" : "")).test(s);
	},
	/*--------------------------------------------------------------------------*/
	isSimpleIP:function(ip){
		ipRegExp = /^(([0-2]*[0-9]+[0-9]+)\.([0-2]*[0-9]+[0-9]+)\.([0-2]*[0-9]+[0-9]+)\.([0-2]*[0-9]+[0-9]+))$/
		return ipRegExp.test(ip);
	},
	/*--------------------------------------------------------------------------*/
	isAlphaLatin:function(string){
		alphaRegExp = /^[0-9a-z]+$/i
		return alphaRegExp.test(string);
	},
	/*--------------------------------------------------------------------------*/
	isNotEmpty:function (string){
		return /\S/.test(string);
	},
	/*--------------------------------------------------------------------------*/
	isEmpty:function(s){
		return !/\S/.test(s);
	},
	/*--------------------------------------------------------------------------*/
	isIntegerInRange:function(n,Nmin,Nmax){
		var num = Number(n);
		if(isNaN(num)){
			return false;
		}
		if(num != Math.round(num)){
			return false;
		}
		return (num >= Nmin && num <= Nmax);
	},
	/*--------------------------------------------------------------------------*/
	isNum:function(number){
		numRegExp = /^[0-9]+$/
		return numRegExp.test(number);
	},
	/*--------------------------------------------------------------------------*/
	isEMailAddr:function(string){
		emailRegExp = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.([a-z]){2,4})$/
		return emailRegExp.test(string);
	},
	/*--------------------------------------------------------------------------*/
	isZipCode:function(zipcode,country){
		if(!zipcode) return false;
		if(!country) format = 'US';
		switch(country){
			case'US': zpcRegExp = /^\d{5}$|^\d{5}-\d{4}$/; break;
			case'MA': zpcRegExp = /^\d{5}$/; break;
			case'CA': zpcRegExp = /^[A-Z]\d[A-Z] \d[A-Z]\d$/; break;
			case'DU': zpcRegExp = /^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/; break;
			case'FR': zpcRegExp = /^\d{5}$/; break;
			case'Monaco':zpcRegExp = /^(MC-)\d{5}$/; break;
		}
		return zpcRegExp.test(zipcode);
	},
	/*--------------------------------------------------------------------------*/
	isDate:function(date,format){
		if(!date) return false;
		if(!format) format = 'FR';
		
		switch(format){
			case'FR': RegExpformat = /^(([0-2]\d|[3][0-1])\/([0]\d|[1][0-2])\/([2][0]|[1][9])\d{2})$/; break;
			case'US': RegExpformat = /^([2][0]|[1][9])\d{2}\-([0]\d|[1][0-2])\-([0-2]\d|[3][0-1])$/; break;
			case'SHORTFR': RegExpformat = /^([0-2]\d|[3][0-1])\/([0]\d|[1][0-2])\/\d{2}$/; break;
			case'SHORTUS': RegExpformat = /^\d{2}\-([0]\d|[1][0-2])\-([0-2]\d|[3][0-1])$/; break;
			case'dd MMM yyyy':RegExpformat = /^([0-2]\d|[3][0-1])\s(Jan(vier)?|Fév(rier)?|Mars|Avr(il)?|Mai|Juin|Juil(let)?|Aout|Sep(tembre)?|Oct(obre)?|Nov(ember)?|Dec(embre)?)\s([2][0]|[1][19])\d{2}$/; break;
			case'MMM dd, yyyy':RegExpformat = /^(J(anuary|u(ne|ly))|February|Ma(rch|y)|A(pril|ugust)|(((Sept|Nov|Dec)em)|Octo)ber)\s([0-2]\d|[3][0-1])\,\s([2][0]|[1][9])\d{2}$/; break;
		}
		
		return RegExpformat.test(date);
	},
	/*--------------------------------------------------------------------------*/
	isMD5:function(string){
		if(!string) return false;
		md5RegExp = /^[a-f0-9]{32}$/;
		return md5RegExp.test(string);
	},
	/*--------------------------------------------------------------------------*/
	isURL:function(string){
		if(!string) return false;
		string = string.toLowerCase();
		urlRegExp = /^(((ht|f)tp(s?))\:\/\/)([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(\/\S*)?$/
		return urlRegExp.test(string);
	},
	/*--------------------------------------------------------------------------*/
	isGuid:function(guid){//guid format : xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx or xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
		if(!guid) return false;
		GuidRegExp = /^[{|\(]?[0-9a-fA-F]{8}[-]?([0-9a-fA-F]{4}[-]?){3}[0-9a-fA-F]{12}[\)|}]?$/
		return GuidRegExp.test(guid);
	},
	/*--------------------------------------------------------------------------*/
	isISBN:function(number){
		if(!number) return false;
		ISBNRegExp = /ISBN\x20(?=.{13}$)\d{1,5}([- ])\d{1,7}\1\d{1,6}\1(\d|X)$/
		return ISBNRegExp.test(number);
	},
	/*--------------------------------------------------------------------------*/
	isSSN:function(number){//Social Security Number format : NNN-NN-NNNN
		if(!number) return false;
		ssnRegExp = /^\d{3}-\d{2}-\d{4}$/
		return ssnRegExp.test(number);
	},
	/*--------------------------------------------------------------------------*/
	isDecimal:function(number){// positive or negative decimal
		if(!number) return false;
		decimalRegExp = /^-?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/
		return decimalRegExp.test(number);
	},
	/*--------------------------------------------------------------------------*/
	isplatform:function(platform){
		//win, mac, nix
		if(!platform) return false;
		var os;
		winRegExp = /\win/i
		if(winRegExp.test(window.navigator.platform)) os = 'win';
		
		macRegExp = /\mac/i
		if(macRegExp.test(window.navigator.platform)) os = 'mac';
		
		nixRegExp = /\unix|\linux|\sun/i
		if(nixRegExp.test(window.navigator.platform)) os = 'nix';
		
		if(platform == os) return true;
		else return false;
	},
	/*--------------------------------------------------------------------------*/
	getValue:function(id){
        if ($(id)==null) return null;
		return $(id).value;
	},
	/*--------------------------------------------------------------------------*/
	addRules:function(rules){
		this.rules_array.push(rules);
	},
	/*--------------------------------------------------------------------------*/
	check:function(){
		this.error_array = [];
		for(var i=0;i<this.rules_array.length;i++){
			switch(this.rules_array[i].option){
				/*--------------------------------------------------------------------------*/
				case'ValidChars':
					if(!this.hasValidChars(getValue(this.rules_array[i].id),this.rules_array[i].chars,false)){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
				case'AlphaLatin':
					if (this.isAlphaLatin(getValue(this.rules_array[i].id))){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
				case'required':
					if (this.isEmpty(getValue(this.rules_array[i].id))){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
				case'integerRange':
					if (!this.isIntegerInRange(getValue(this.rules_array[i].id),this.rules_array[i].Min,this.rules_array[i].Max)){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
				case'Number':
					if (!this.isNum(getValue(this.rules_array[i].id))){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
				case'email':
					if (!this.isEMailAddr(getValue(this.rules_array[i].id))){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
				case'zipCode':
					if (!this.isZipCode(getValue(this.rules_array[i].id),this.rules_array[i].country)){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
				case'date':
					if(!this.isDate(getValue(this.rules_array[i].id),this.rules_array[i].format)){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
				case'url':
					if(!this.isURL(getValue(this.rules_array[i].id))){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
				case'Decimal':
					if(!this.isDecimal(getValue(this.rules_array[i].id))){
						this.error_array.push(this.rules_array[i].error);
						this.e = false;
					}
				break;
				/*--------------------------------------------------------------------------*/
			}
		}
	},
	/*--------------------------------------------------------------------------*/
	Apply:function(el){
		this.check();
		if(this.e){
			return true;
		}else{
			var endMsg = this.error_array;
			if(!el){
				alert(this.error_array.toString().replace(/\,/gi,"\n"));
			}else{
				$(el).innerHTML = this.error_array.toString().replace(/\,/gi,"<br/>");
			}
			return false;
		}
	}
	/*--------------------------------------------------------------------------*/
}
