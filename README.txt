Telephone Validation
========================

This is basic module which brings validation to Telephone field instance
(telephone_default widget). It uses libphonenumber-php library (port of google's
libphonenumber library).
Module can discover where phone number comes from and if it's valid or not.

# Dependencies

- telephone
- composer_manager
- field_ui

# Instalation

Just enable module. You will might need to use drush composer extension to
install libphonenumber library. If so, follow composer_manager documentation
page https://www.drupal.org/node/2405805
After installation you'll find new fieldset under each telephone field instance
where you can decide how number should looks like and what makes it valid.

# Credits

Jakub Piasecki for Ny Media AS
