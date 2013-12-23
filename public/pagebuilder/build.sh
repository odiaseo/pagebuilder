#build css
node libs/r.js -o cssIn=css/style.css out=css/style.min.css  optimizeCss=standard

#build js
node libs/r.js -o buildJs.js