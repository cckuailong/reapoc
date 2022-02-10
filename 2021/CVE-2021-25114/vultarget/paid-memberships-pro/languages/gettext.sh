#---------------------------
# This script generates a new pmpro.pot file for use in translations.
# To generate a new pmpro.pot, cd to the main /paid-memberships-pro/ directory,
# then execute `languages/gettext.sh` from the command line.
# then fix the header info (helps to have the old pmpro.pot open before running script above)
# then execute `cp languages/paid-memberships-pro.pot languages/paid-memberships-pro.po` to copy the .pot to .po
# then execute `msgfmt languages/paid-memberships-pro.po --output-file languages/paid-memberships-pro.mo` to generate the .mo
#---------------------------
echo "Updating paid-memberships-pro.pot... "
xgettext -j -o languages/paid-memberships-pro.pot \
--default-domain=paid-memberships-pro \
--language=PHP \
--keyword=_ \
--keyword=__ \
--keyword=_e \
--keyword=_ex \
--keyword=_n \
--keyword=_x \
--keyword=esc_html__ \
--keyword=esc_html_e \
--keyword=esc_html_x \
--keyword=esc_attr__ \
--keyword=esc_attr_e \
--keyword=esc_attr_x \
--sort-by-file \
--package-version=1.0 \
--msgid-bugs-address="jason@strangerstudios.com" \
$(find . -name "*.php")
echo "Done!"