var forever = require("forever-monitor");

var child = new(forever.Monitor)('app.js', {
    'silent': false,
    'sourceDir': '.',
    'watch': true,
    'watchDirectory': '.',
    'watchIgnoreDotFiles': null,
    'watchIgnorePatterns': null
});

child.start();