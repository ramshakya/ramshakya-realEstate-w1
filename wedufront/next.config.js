const withPlugins = require('next-compose-plugins')
const withBundleAnalyzer = require('@next/bundle-analyzer')({
  enabled: process.env.ANALYZE === 'true',
})
const withPluginData = withPlugins([
  [withBundleAnalyzer],
  // your other plugins here
])

module.exports = withPlugins([  
  [withBundleAnalyzer],
  // your other plugins here
],{
  reactStrictMode: true,
  images: {
    domains: ['3.96.220.206','34.130.143.118','localhost','searchrealtymls.s3.us-east-2.amazonaws.com','via.placeholder.com','3.144.136.139','3.14.96.83','wedumlsimages.s3.us-east-2.amazonaws.com','127.0.0.1','searchrealty.ca','panel.wedu.ca','44.193.199.108','3.134.92.82','admin.housen.ca','3.98.17.162'],
    deviceSizes: [640, 750, 828, 1080, 1200, 1920, 2048, 3840],
    imageSizes: [16, 32, 48, 64, 96, 128, 256, 384],
  },
  i18n: {
    locales: ['en'],
    defaultLocale: 'en',
  },
}),
{
  "exclude": ["script"]
}
// module.exports = {
//   reactStrictMode: true,
   
// }
