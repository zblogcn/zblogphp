
import { config } from 'dotenv';
import browsersync from 'rollup-plugin-browsersync'
import copy from 'rollup-plugin-copy'
import postcss from "rollup-plugin-postcss";
// import { purgeCSSPlugin } from '@fullhuman/postcss-purgecss';

// 获取当前及应用目录
const curPath = process.cwd();
const appPath = curPath.replace(/\/[^/]+$/, "");
// 应用文件夹名
const appName = appPath.replace(/.*\//, "");
// 产物复制到的目录
// const distJS = `${appPath}/script/`;
const distCSS = `${appPath}/style/`;

// 加载环境变量
const envConfig = config({ path: `${curPath}/.env` }).parsed;

const defConfig = {
  input: `src/main.js`,
  output: {
    file: `dist/${appName}.js`,
    format: "esm",
    banner: "/* eslint-disable */\n",
  },
  plugins: [
    postcss({
      // 该路径相对于上边的 output.file
      extract: `${appName}.css`,
      use: {
        sass: {
          silenceDeprecations: ['legacy-js-api'],
        }
      },
      // plugins: [
      //   purgeCSSPlugin({
      //     content: [`${appPath}/template/*.php`],
      //   }),
      // ],
    }),
    copy({
      targets: [
        // {
        //   src: `dist/${appName}.js`,
        //   dest: distJS,
        // },
        {
          src: `dist/${appName}.css`,
          dest: distCSS,
        },
      ],
      verbose: true,
      hook: 'writeBundle',
    }),
  ],
};

if (process.env.NODE_ENV === "dev") {
  defConfig.plugins.push(
    browsersync({
      proxy: envConfig.PROXY || 'http://localhost',
      files: [
        `${appPath}/**/*.php`,
        `${appPath}/style/**/*.css`,
        // `${appPath}/script/**/*.js`,
      ],
    }),
  );
}

export default defConfig;
