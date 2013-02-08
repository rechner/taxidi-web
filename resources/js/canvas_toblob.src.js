/* Copyright 2012 Nathan Lex
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

(function(_global) {
	if (HTMLCanvasElement && !HTMLCanvasElement.prototype.toBlob) {
		//http://www.khronos.org/registry/typedarray/specs/latest/
		var b64_keystr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
		var b64_decode = function(data) {
			data = data.replace(/[^A-Za-z0-9\+\/\=]/g, "");
	
			var arraybuffer = new ArrayBuffer(Math.ceil((3 * data.length) / 4.0)),
			    uint8array  = new Uint8Array(arraybuffer),
			    i = 0, j = 0, val1, val2, val3, val4;
	
			for (i = 0; i < uint8array.length; i += 3) {
				val1 = b64_keystr.indexOf(data.charAt(j++));
				val2 = b64_keystr.indexOf(data.charAt(j++));
				val3 = b64_keystr.indexOf(data.charAt(j++));
				val4 = b64_keystr.indexOf(data.charAt(j++));

				                uint8array[i    ] = ( val1       << 2) | (val2 >> 4);	
				if (val3 != 64) uint8array[i + 1] = ((val2 & 15) << 4) | (val3 >> 2);
				if (val4 != 64) uint8array[i + 2] = ((val3 &  3) << 6) |  val4;
			}
	
			return arraybuffer;
		}
		
		//http://www.whatwg.org/specs/web-apps/current-work/multipage/the-canvas-element.html#dom-canvas-toblob
		HTMLCanvasElement.prototype.toBlob = function(callback, type) {
			var type = type || "image/png";
			
			if (this.mozGetAsFile) { // Gecko ≥ 2.0
				callback(this.mozGetAsFile("canvas", type));
				return;
			}
			
			var dataparts   = this.toDataURL.apply(this, Array.prototype.slice.call(arguments, 1)).split(","),
			    blobbuilder = new (_global.BlobBuilder || _global.WebKitBlobBuilder || _global.MozBlobBuilder || _global.MSBlobBuilder),
			    result      = null;
			
			if (this.width && this.height) {
				if(/\s*;\s*base64\s*(?:;|$)/i.test(dataparts[0])) {
					blobbuilder.append(b64_decode(dataparts[1]));
				} else {
					blobbuilder.append(decodeURIComponent(dataparts[1]));
				}
				result = blobbuilder.getBlob(type);
			}
			
			callback(result);
		}
	}
}(this));
