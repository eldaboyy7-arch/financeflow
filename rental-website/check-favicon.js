const fs = require('fs')
const path = require('path')

const pngPath = path.join(__dirname, 'public', 'favicon.png')
const png = fs.readFileSync(pngPath)

// Get PNG dimensions from bytes 16-24
const width = png.readUInt32BE(16)
const height = png.readUInt32BE(20)
console.log('favicon.png size:', png.length, 'bytes')
console.log('Dimensions:', width, 'x', height, 'px')
