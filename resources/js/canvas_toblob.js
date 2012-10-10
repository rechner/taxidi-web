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

(function(d){if(HTMLCanvasElement&&!HTMLCanvasElement.prototype.toBlob){var j=function(b){for(var b=b.replace(/[^A-Za-z0-9\+\/\=]/g,""),e=new ArrayBuffer(Math.ceil(3*b.length/4)),c=new Uint8Array(e),a=0,f=0,d,h,g,i,a=0,z="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";a<c.length;a+=3)d=z.indexOf(b.charAt(f++)),h=z.indexOf(b.charAt(f++)),g=z.indexOf(b.charAt(f++)), i=z.indexOf(b.charAt(f++)),c[a]=d<<2|h>>4,64!=g&&(c[a+1]=(h&15)<<4|g>>2),64!=i&&(c[a+2]=(g&3)<<6|i);return e};HTMLCanvasElement.prototype.toBlob=function(b,e){e=e||"image/png";if(this.mozGetAsFile)b(this.mozGetAsFile("canvas",e));else{var c=this.toDataURL.apply(this,Array.prototype.slice.call(arguments,1)).split(","),a=new (d.BlobBuilder||d.WebKitBlobBuilder||d.MozBlobBuilder||d.MSBlobBuilder),f=null;this.width&&this.height&&(/\s*;\s*base64\s*(?:;|$)/i.test(c[0])? a.append(j(c[1])):a.append(decodeURIComponent(c[1])),f=a.getBlob(e));b(f)}}}})(this);
