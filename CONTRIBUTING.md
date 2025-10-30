# Contributing to BT Site Recommendations

Thank you for your interest in contributing to BT Site Recommendations! This document provides guidelines and instructions for contributing.

## Code of Conduct

- Be respectful and inclusive
- Focus on constructive feedback
- Help create a welcoming environment for all contributors

## How Can I Contribute?

### Reporting Bugs

Before creating a bug report:
1. Check the [existing issues](https://github.com/Bartek55/bt_site_recommendations/issues)
2. Verify you're using the latest version
3. Test with default WordPress theme and no other plugins

When creating a bug report, include:
- WordPress version
- PHP version
- Plugin version
- Browser and version (for UI issues)
- Detailed steps to reproduce
- Expected vs actual behavior
- Screenshots or error messages

### Suggesting Enhancements

Enhancement suggestions are welcome! Please:
1. Check if it's already suggested
2. Provide a clear use case
3. Explain why it would benefit users
4. Consider implementation complexity

### Pull Requests

#### Setup Development Environment

1. **Fork the repository**
   ```bash
   # On GitHub, click "Fork" button
   ```

2. **Clone your fork**
   ```bash
   git clone https://github.com/YOUR-USERNAME/bt_site_recommendations.git
   cd bt_site_recommendations
   ```

3. **Create a branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/bug-description
   ```

#### Development Guidelines

**PHP Standards**
- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Use PHP 7.4+ features when appropriate
- Add PHPDoc comments for all functions and classes
- Validate syntax: `php -l file.php`

**JavaScript Standards**
- Follow WordPress JavaScript standards
- Use jQuery for consistency with WordPress
- Add comments for complex logic
- Test in modern browsers

**CSS Standards**
- Follow WordPress CSS Coding Standards
- Use BEM-like naming for custom classes (bt-component-element)
- Ensure responsive design
- Test across different admin themes

**File Organization**
```
bt-site-recommendations/
├── assets/
│   ├── css/          # Stylesheets
│   └── js/           # JavaScript files
├── includes/
│   ├── class-*.php   # Main classes
│   └── views/        # Template files
├── tests/            # Test files
└── bt-site-recommendations.php  # Main plugin file
```

#### Making Changes

1. **Write meaningful commits**
   ```bash
   git commit -m "Add page size threshold configuration option"
   ```

2. **Keep changes focused**
   - One feature/fix per pull request
   - Don't mix refactoring with new features

3. **Test your changes**
   ```bash
   # Run basic tests
   php tests/test-basic.php
   
   # Check PHP syntax
   find . -name "*.php" -exec php -l {} \;
   ```

4. **Update documentation**
   - Update README.md if adding features
   - Update INSTALL.md if changing installation
   - Add inline comments for complex code

#### Submitting Pull Request

1. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

2. **Create Pull Request**
   - Go to original repository on GitHub
   - Click "New Pull Request"
   - Select your branch
   - Fill in the PR template

3. **PR Description Should Include**
   - What changes were made
   - Why the changes were needed
   - How to test the changes
   - Related issue numbers (#123)
   - Screenshots for UI changes

4. **Review Process**
   - Maintainers will review your PR
   - Address any feedback or requests
   - Keep the PR updated with main branch
   - Be patient and respectful

## Development Areas

### High Priority
- [ ] Add unit tests with PHPUnit
- [ ] Implement scheduled analysis (cron jobs)
- [ ] Add export functionality (PDF/CSV)
- [ ] Mobile-specific performance analysis
- [ ] Multi-page analysis (not just homepage)

### Medium Priority
- [ ] Integration with Google PageSpeed Insights API
- [ ] Historical tracking and graphs
- [ ] Waterfall charts for resource loading
- [ ] Custom notification preferences
- [ ] White-label options

### Low Priority
- [ ] Translations (internationalization)
- [ ] Custom report templates
- [ ] Integration with other SEO plugins
- [ ] Advanced scheduling options
- [ ] Multi-site support

## Testing

### Manual Testing Checklist

When testing changes:

- [ ] Plugin activates without errors
- [ ] Plugin deactivates cleanly
- [ ] Settings save correctly
- [ ] Analysis completes successfully
- [ ] Recommendations display properly
- [ ] UI is responsive on mobile
- [ ] No JavaScript console errors
- [ ] No PHP warnings or notices
- [ ] Works with WordPress 5.0+
- [ ] Works with PHP 7.4+
- [ ] Compatible with popular themes
- [ ] Compatible with common plugins

### Automated Tests

Run basic tests:
```bash
php tests/test-basic.php
```

Expected output: All tests should pass with ✓ marks.

## Documentation

When adding features, update:

1. **Code Comments**
   - Add PHPDoc blocks
   - Explain complex logic
   - Note any WordPress-specific quirks

2. **README.md**
   - Update feature list
   - Add usage examples
   - Update screenshots section

3. **INSTALL.md**
   - Add new requirements
   - Update installation steps if needed

4. **readme.txt**
   - WordPress.org format
   - Update changelog
   - Add FAQ entries

## Release Process

For maintainers:

1. Update version numbers:
   - bt-site-recommendations.php header
   - readme.txt Stable tag
   - README.md changelog

2. Update CHANGELOG.md

3. Create GitHub release:
   - Tag: v1.x.x
   - Title: Version 1.x.x
   - Description: Changelog

4. Test in clean WordPress install

## Questions?

- Open an issue for general questions
- Tag as "question" label
- Check existing closed issues

## License

By contributing, you agree that your contributions will be licensed under the GPL-3.0 License.

---

Thank you for contributing to BT Site Recommendations! 🎉
