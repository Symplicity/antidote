'use strict';

describe('The main view', function() {
    var page;

    beforeEach(function() {
        browser.get('http://127.0.0.1/');
        page = require('./main.po');
    });

    it('should identify the system', function() {
        expect(page.menuButtons.count()).toBe(5);
        expect(page.homeCards.getText()).toEqual(['Insurance', 'Personalized', 'Trusted']);
    });
});
