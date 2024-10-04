const w3IsMobile= window.innerWidth<768?1:0, rootMargin = '0px 0px '+w3_lazy_load_by_px+'px 0px',windowHeight = window.innerHeight;
function w3ToWebp(elementImg) {
	for (var ig = 0; ig < elementImg.length; ig++) {
		if (elementImg[ig].getAttribute("data-src") != null && elementImg[ig].getAttribute("data-src") != "") {
			var datasrc = elementImg[ig].getAttribute("data-src");
			elementImg[ig].setAttribute("data-src", datasrc.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path));
		}
		if (elementImg[ig].getAttribute("data-srcset") != null && elementImg[ig].getAttribute("data-srcset") != "") {
			var datasrcset = elementImg[ig].getAttribute("data-srcset");
			elementImg[ig].setAttribute("data-srcset", datasrcset.replace(/w3.webp/g, "").split(w3_webp_path).join(w3_upload_path));
		}
		if (elementImg[ig].src != null && elementImg[ig].src != "") {
			var src = elementImg[ig].src;
			elementImg[ig].src = src.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path);
		}
		if (elementImg[ig].srcset != null && elementImg[ig].srcset != "") {
			var srcset = elementImg[ig].srcset;
			elementImg[ig].srcset = srcset.replace(/w3.webp/g, "").split(w3_webp_path).join(w3_upload_path);
		}
	}
}
function fixWebp() {
	if (!w3HasWebP) {
		var elementNames = ["*"];
		w3ToWebp(document.querySelectorAll("img[data-src$='w3.webp']"));
		w3ToWebp(document.querySelectorAll("img[src$='w3.webp']"));
		elementNames.forEach(function(tagName) {
			var tags = document.getElementsByTagName(tagName);
			var numTags = tags.length;
			for (var i = 0; i < numTags; i++) {
				var tag = tags[i];
				var style = tag.currentStyle || window.getComputedStyle(tag, false);
				var bg = style.backgroundImage;
				if (bg.match("w3.webp")) {
					if (document.all) {
						tag.style.setAttribute("cssText", ";background-image: " + bg.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path) + " !important;");
					} else {
						tag.setAttribute("style", tag.getAttribute("style") + ";background-image: " + bg.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path) + " !important;");
					}
				}
			}
		});
	}
}
function w3ChangeWebp() {
	if (bg.match("w3.webp")) {
		var style1 = {};
		if (document.all) {
			tag.style.setAttribute("cssText", "background-image: " + bg.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path) + " !important");
			style1 = tag.currentStyle || window.getComputedStyle(tag, false);
		} else {
			tag.setAttribute("style", "background-image: " + bg.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path) + " !important");
			style1 = tag.currentStyle || window.getComputedStyle(tag, false);
		}
	}
}
var w3HasWebP = false
  , w3Bglazyload = 1;
var img = new Image();
img.onload = function() {
	w3HasWebP = !!(img.height > 0 && img.width > 0);
}
;
img.onerror = function() {
	w3HasWebP = false;
	fixWebp();
}
;
img.src = blank_image_webp_url;
function w3_events_on_end_js() {
	if(!document.getElementsByTagName('html')[0].classList.contains('jsload')){
		setTimeout(w3_events_on_end_js,100);
		return;
	}
	w3Bglazyload = 0;
	const lazyImages = [].slice.call(document.querySelectorAll("img[data-class='LazyLoad']"));
	w3LazyLoadResource(lazyImages, 'img');
	setTimeout(function(){
		const bgImg = [].slice.call(document.querySelectorAll("div[data-w3BgLazy='1'], section[data-w3BgLazy='1']"));
		w3LazyLoadResource(bgImg, 'bgImg');
	},1000);
}
let w3LoadResource = {};
let lazyObserver = {};
function w3LazyLoadResource(lazyResources, resource) {
	if ("IntersectionObserver"in window) {
		lazyObserver[resource] = new IntersectionObserver(function(entries, observer) {
			entries.forEach(function(entry) {
				const w3BodyRect = document.body.getBoundingClientRect();
				if (entry.isIntersecting || ((w3BodyRect.top != 0 || window.w3Html['class'].indexOf('w3_start') != -1) && (entry.boundingClientRect.top - windowHeight + w3BodyRect.top) < w3_lazy_load_by_px)) {
					const compStyles = window.getComputedStyle(entry.target);
					if (compStyles.getPropertyValue("opacity") != 0) {
						w3LoadResource[resource](entry);
					}
				}
			});
		},{
		  rootMargin: rootMargin,
		  scrollMargin: rootMargin,
		  threshold: 0.0
		});
		lazyResources.forEach(function(elem) {
			if(elem.tagName == 'IMG' || elem.tagName == 'PICTURE')
				elem.removeAttribute('data-class');
			elem.setAttribute("data-w3BgLazy",1);
			lazyObserver[resource].observe(elem);
		});
	} else {
		lazyResources.forEach(function(lazyResource) {
			w3LoadResource['rl' + resource](lazyResource);
		});
	}
}
w3LoadResource['rlvideo'] = function(lazyResource) {
	lazyloadVideo(lazyResource);
	delete lazyResource.dataset.class;
}
w3LoadResource['video'] = function(entry) {
	let lazyVideo = entry.target;
	lazyloadVideo(lazyVideo);
	delete lazyVideo.dataset.class;
	lazyObserver['video'].unobserve(lazyVideo);
}
w3LoadResource['iframe'] = function(entry) {
	let lazyIframe = entry.target;
	lazyIframe.src = lazyIframe.dataset.src;
	delete lazyIframe.dataset.class;
	lazyObserver['iframe'].unobserve(lazyIframe);
}
w3LoadResource['rliframe'] = function(lazyResource) {
	lazyResource.src = lazyResource.dataset.src ? lazyResource.dataset.src : lazyResource.src;
	delete lazyResource.dataset.class;
}
w3LoadResource['bgImg'] = function(entry) {
	let lazyBg = entry.target;
	lazyBg.removeAttribute("data-w3BgLazy");
	lazyObserver['bgImg'].unobserve(lazyBg);
}
w3LoadResource['rlbgImg'] = function(lazyResource) {
	const lazyBgStyle = document.getElementById("w3_bg_load");
	if(lazyBgStyle !== null){
		lazyBgStyle.remove();
	}
}
w3LoadResource['picture'] = function(entry){
	const picture = entry.target;
	const sources = picture.querySelectorAll('source');
	const img = picture.querySelector('img');
	sources.forEach(source => {
		if (source.dataset.srcset) {
			source.srcset = source.dataset.srcset;
			source.removeAttribute('data-srcset');
		}
	});
	w3LoadImg(img);
	lazyObserver['img'].unobserve(picture);
}
w3LoadImg = function(lazyImage){
	if (w3IsMobile && lazyImage.getAttribute("data-mob-src")) {
		lazyImage.src = lazyImage.getAttribute("data-mob-src");
	} else {
		lazyImage.src = lazyImage.dataset.src ? lazyImage.dataset.src : lazyImage.src;
	}
	if(lazyImage.dataset.srcset)
		lazyImage.srcset = lazyImage.dataset.srcset;
	lazyImage.removeAttribute('data-srcset');
	lazyImage.removeAttribute('data-src');
}
w3LoadResource['img'] = function(entry) {
	let lazyImage = entry.target;
	w3LoadImg(lazyImage);
	lazyObserver['img'].unobserve(lazyImage);
}
w3LoadResource['rlimg'] = function(lazyResource) {
	lazyResource.src = lazyResource.dataset.src ? lazyResource.dataset.src : lazyResource.src;
	lazyResource.srcset = lazyResource.dataset.srcset ? lazyResource.dataset.srcset : lazyResource.srcset;
	delete lazyResource.dataset.class;
}
function w3_events_on_start_js() {
	var lazyvideos = document.getElementsByTagName("videolazy");
	convertToVideoTag(lazyvideos);
	loadVideos();
	var lazyIframes = [].slice.call(document.querySelectorAll("iframelazy[data-class='LazyLoad']"));
	convertToIframes(lazyIframes);
	setInterval(function(){
		loadVideos();
	},1000);
}
function loadVideos(){
	var lazyVideos = [].slice.call(document.querySelectorAll("video[data-class='LazyLoad'], audio[data-class='LazyLoad']"));
	w3LazyLoadResource(lazyVideos, 'video');
}
function convertToIframes(lazyIframes) {
	lazyIframes.forEach(function(lazyIframe) {
		var elem = document.createElement("iframe");
		var index;
		for (index = lazyIframe.attributes.length - 1; index >= 0; --index) {
			elem.attributes.setNamedItem(lazyIframe.attributes[index].cloneNode());
		}
		elem.src = lazyIframe.dataset.src;
		lazyIframe.parentNode.replaceChild(elem, lazyIframe);
		delete elem.dataset.class;
	});
	var lazyIframes = [].slice.call(document.querySelectorAll("iframe[data-class='LazyLoad']"));
	w3LazyLoadResource(lazyIframes, 'iframe');
}
startLazyLoad();
setInterval(function(){
	startImgLazyLoad();
},1000);
function startImgLazyLoad(){
	var lazyImages = [].slice.call(document.querySelectorAll("img[data-class='LazyLoad'],img[src*='data:image/svg']"));
	w3LazyLoadResource(lazyImages, 'img');
	var lazyPics = [].slice.call(document.querySelectorAll("picture[data-class='LazyLoad']"));
	w3LazyLoadResource(lazyPics, 'picture');
}
function startLazyLoad(){
	startImgLazyLoad();
	var lazyBgs = document.querySelectorAll("div, section, iframelazy, iframe[data-class='LazyLoad']");
	w3LazyLoadResource(lazyBgs, 'bgImg');
	w3LoadResource['rlbgImg']();
}
function lazyloadVideo(lazyVideo) {
	if (typeof (lazyVideo.getElementsByTagName("source")[0]) == "undefined") {
		lazyloadVideoSource(lazyVideo);
	} else {
		var sources = lazyVideo.getElementsByTagName("source");
		for (var j = 0; j < sources.length; j++) {
			var source = sources[j];
			lazyloadVideoSource(source);
		}
	}
}
function lazyloadVideoSource(source) {
	var src = source.getAttribute("data-src") ? source.getAttribute("data-src") : source.src;
	var srcset = source.getAttribute("data-srcset") ? source.getAttribute("data-srcset") : "";
	if (source.srcset != null & source.srcset != "") {
		source.srcset = srcset;
	}
	if (typeof (source.getElementsByTagName("source")[0]) == "undefined") {
		if (source.tagName == "SOURCE") {
			source.src = src;
			source.parentNode.load();
			if (source.parentNode.getAttribute("autoplay") !== null) {
				source.parentNode.play();
			}
		} else {
			source.src = src;
			source.load();
			if (source.getAttribute("autoplay") !== null) {
				source.play();
			}
		}
	} else {
		source.parentNode.src = src;
	}
	delete source.dataset.class;
}
function convertToVideoTag(imgs) {
	const t = imgs.length > 0 ? imgs[0] : "";
	if (t) {
		delete imgs[0];
		var newelem = document.createElement("video");
		var index;
		for (index = t.attributes.length - 1; index >= 0; --index) {
			newelem.attributes.setNamedItem(t.attributes[index].cloneNode());
		}
		newelem.innerHTML = t.innerHTML;
		t.parentNode.replaceChild(newelem, t);
		if (typeof (newelem.getAttribute("data-poster")) == "string") {
			newelem.setAttribute("poster", newelem.getAttribute("data-poster"));
		}
		convertToVideoTag(imgs);
	}
}