({
    baseUrl: "./libs",
    name: "app",
    out: "./libs/app.min.js",
    skipModuleInsertion: true,
    findNestedDependencies: true,
    preserveLicenseComments: false,
    mainConfigFile: './config.js',
    optimize: 'uglify2',
    uglify2: {
        mangle: true
    }
})