#!/usr/bin/env bash

# SPDX-FileCopyrightText: Ambrose Larkin <ambroseLarkin@goldendelta.space>
# SPDX-License-Identifier: AGPL-3.0-or-later

set -e
tmp_dir="$(mktemp -d)"
trap 'rm -r "$tmp_dir"' EXIT

wget -q -O "$tmp_dir/secrets.tar.gz" https://github.com/fstar-dev/secrets-extension/releases/latest/download/secrets.tar.gz
wget -q -O "$tmp_dir/secrets.sha256" https://github.com/fstar-dev/secrets-extension//releases/latest/download/secrets.sha256
echo "Release SHA256 sum:"
cat "$tmp_dir/secrets.sha256"

checksum="$(cd "$tmp_dir"; sha256sum "secrets.tar.gz")"
[[ "$checksum" == "$(cat "$tmp_dir/secrets.sha256")" ]] || {
	echo "Checksum mismatch!" >&2
	echo "Expected: $(cat "$tmp_dir/secrets.sha256")"
	echo "Was:      $checksum"
	exit 1
}

openssl dgst -sha512 -sign "$HOME/.nextcloud/certificates/secrets.key" "$tmp_dir/secrets.tar.gz" | openssl base64


