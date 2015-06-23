'use strict';

describe('The main view', function() {
    var page;

    beforeEach(function() {
        browser.get('http://localhost:3000/index.html');
        page = require('./main.po');
    });

    it('should identify the system', function() {
        expect(page.toolbarEl.getText()).toContain('ANTIDOTE');
    });
});
