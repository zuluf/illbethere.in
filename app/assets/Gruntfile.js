module.exports = function(grunt) {
	var task, htaccess;

	task = process.argv[2];

	htaccess = function htaccess (fs, fd, done) {
		var date, expires, content;

		date = new Date();
		expires = new Date();
		expires.setMonth(date.getMonth() + 11);

		content =
			'<IfModule mod_headers.c>\n' +
				'\t<FilesMatch "\\.(bmp|css|flv|gif|ico|jpg|jpeg|js|pdf|png|svg|swf|tif|tiff)$">\n' +
					'\t\tHeader unset ETag\n' +
					'\t\tHeader set Last-Modified "' + date.toUTCString() + '"\n' +
					'\t\tHeader set Cache-Control "public, max-age=31536000"\n' +
					'\t\tHeader set Expires "' + expires.toUTCString() + '"\n' +
				'\t</FilesMatch>\n' +
			'</IfModule>\n' +
			'FileETag None\n' +
			'<IfModule mod_deflate.c>\n' +
				'\t# force deflate for mangled headers\n' +
				'\t# developer.yahoo.com/blogs/ydn/posts/2010/12/pushing-beyond-gzipping/\n' +
				'\t<IfModule mod_setenvif.c>\n' +
					'\t\t<IfModule mod_headers.c>\n' +
						'\t\t\tSetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding\n' +
						'\t\t\tRequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding\n' +
					'\t\t</IfModule>\n' +
				'\t</IfModule>\n\n' +
				'\t# HTML, TXT, CSS, JavaScript, JSON, XML, HTC:\n' +
				'\t<IfModule mod_filters.c>\n' +
					'\t\tFilterDeclare   COMPRESS\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/html\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/css\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/plain\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/xml\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/x-component\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/javascript\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/json\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/xml\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/xhtml+xml\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/rss+xml\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/atom+xml\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/vnd.ms-fontobject\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $image/svg+xml\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/x-font-ttf\n' +
					'\t\tFilterProvider  COMPRESS  DEFLATE resp=Content-Type $font/opentype\n' +
					'\t\tFilterChain     COMPRESS\n' +
					'\t\tFilterProtocol  COMPRESS  DEFLATE change=yes;byteranges=no\n' +
				'\t</IfModule>\n' +
			'</IfModule>';

		fs.writeSync(fd, content);
		done();
	};

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			build: {
				src: ['js/src/libs/**/*.js', 'js/src/ibt/**/*.js',  'js/src/models/**/*.js', 'js/src/widgets/**/*.js',  'js/src/scripts/**/*.js', 'js/src/pages/**/*.js', 'js/app.js'],
				dest: 'dist/app.min.js'
			}
		},
		less: {
			production: {
				cleancss: true,
				ieCompat: true,
				compress : true,
				files: {
					"dist/app.min.css": "less/main.less"
				}
			}
		},
		cssmin: {
			css:{
				src: "dist/app.min.css",
				dest: "dist/app.min.css"
			}
		},
		'file-creator': {
			"version" : {
				"../.htaccess": function(fs, fd, done) {
					htaccess(fs, fd, done);
				}
			}
		},
		concat: {
			dist: {
				src: ['js/src/libs/**/*.js', 'js/src/ibt/**/*.js',  'js/src/models/**/*.js',  'js/src/widgets/**/*.js',  'js/src/scripts/**/*.js', 'js/src/pages/**/*.js', 'js/app.js'],
				dest: 'dist/app.min.js'
			},
		}
	});

	grunt.loadNpmTasks('grunt-collect-css-images');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-file-creator');

	grunt.registerTask('build', ['uglify:build', 'less:production', 'cssmin:css', 'file-creator']);
	grunt.registerTask('css', ['less:production', 'cssmin:css']);
	grunt.registerTask('js', ['uglify:build']);

	grunt.registerTask('dev', ['less:production']);
};