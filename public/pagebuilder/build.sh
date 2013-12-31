#build css
node libs/r.js -o cssIn=css/style.css out=css/pagebuilder.min.css  optimizeCss=standard

#build js
node libs/r.js -o buildJs.js