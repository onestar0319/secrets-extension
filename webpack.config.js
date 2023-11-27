// SPDX-FileCopyrightText: Ambrose Larkin <ambroseLarkin@goldendelta.space>
// SPDX-License-Identifier: AGPL-3.0-or-later
const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry = {
	public: path.resolve(path.join('src', 'public.js')),
	main: path.resolve(path.join('src', 'main.js')),
}

module.exports = webpackConfig
