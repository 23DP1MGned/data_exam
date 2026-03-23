function hashSeed(seed) {
  return Array.from(String(seed)).reduce((acc, char) => acc + char.charCodeAt(0), 0)
}

function pseudo(seed, offset) {
  const value = Math.sin(hashSeed(`${seed}-${offset}`) * 12.9898) * 43758.5453
  return value - Math.floor(value)
}

function hslToHex(h, s, l) {
  const saturation = s / 100
  const lightness = l / 100
  const c = (1 - Math.abs(2 * lightness - 1)) * saturation
  const x = c * (1 - Math.abs(((h / 60) % 2) - 1))
  const m = lightness - c / 2
  let r = 0
  let g = 0
  let b = 0

  if (h < 60) [r, g, b] = [c, x, 0]
  else if (h < 120) [r, g, b] = [x, c, 0]
  else if (h < 180) [r, g, b] = [0, c, x]
  else if (h < 240) [r, g, b] = [0, x, c]
  else if (h < 300) [r, g, b] = [x, 0, c]
  else [r, g, b] = [c, 0, x]

  const toHex = (value) => Math.round((value + m) * 255).toString(16).padStart(2, '0')
  return `#${toHex(r)}${toHex(g)}${toHex(b)}`
}

function pickPalette(seed) {
  const hue = Math.round(pseudo(seed, 101) * 360)
  const hueShift = 18 + Math.round(pseudo(seed, 102) * 42)
  const secondaryHue = (hue + hueShift) % 360
  const primary = hslToHex(hue, 72 + Math.round(pseudo(seed, 103) * 14), 47 + Math.round(pseudo(seed, 104) * 8))
  const secondary = hslToHex(secondaryHue, 78 + Math.round(pseudo(seed, 105) * 12), 70 + Math.round(pseudo(seed, 106) * 10))

  return [primary, secondary]
}

export function createAvatarDataUri(seed) {
  const [primary, secondary] = pickPalette(seed)
  const accent = secondary.replace('#', '%23')
  const ring = secondary.replace('#', '%23')
  const fillA = primary.replace('#', '%23')
  const fillB = secondary.replace('#', '%23')
  const shapeTypeA = Math.floor(pseudo(seed, 1) * 3)
  const shapeTypeB = Math.floor(pseudo(seed, 2) * 3)
  const shapeTypeC = Math.floor(pseudo(seed, 3) * 3)
  const x1 = Math.round(16 + pseudo(seed, 4) * 20)
  const y1 = Math.round(16 + pseudo(seed, 5) * 20)
  const s1 = Math.round(18 + pseudo(seed, 6) * 16)
  const x2 = Math.round(48 + pseudo(seed, 7) * 18)
  const y2 = Math.round(16 + pseudo(seed, 8) * 18)
  const s2 = Math.round(16 + pseudo(seed, 9) * 18)
  const x3 = Math.round(18 + pseudo(seed, 10) * 44)
  const y3 = Math.round(50 + pseudo(seed, 11) * 18)
  const s3 = Math.round(22 + pseudo(seed, 12) * 18)
  const r1 = Math.round(6 + pseudo(seed, 13) * 12)
  const r2 = Math.round(6 + pseudo(seed, 14) * 12)
  const r3 = Math.round(8 + pseudo(seed, 15) * 14)
  const lineX1 = Math.round(14 + pseudo(seed, 16) * 18)
  const lineY1 = Math.round(58 + pseudo(seed, 17) * 16)
  const lineX2 = Math.round(64 + pseudo(seed, 18) * 18)
  const lineY2 = Math.round(24 + pseudo(seed, 19) * 18)
  const strokeWidth = Math.round(5 + pseudo(seed, 20) * 4)
  const rotateA = Math.round(-24 + pseudo(seed, 21) * 48)
  const rotateB = Math.round(-24 + pseudo(seed, 22) * 48)

  function shape(type, x, y, size, radius, fill, opacity, rotate = 0) {
    if (type === 0) {
      return `<circle cx="${x + size / 2}" cy="${y + size / 2}" r="${Math.round(size / 2)}" fill="${fill}" opacity="${opacity}"/>`
    }

    if (type === 1) {
      return `<rect x="${x}" y="${y}" width="${size}" height="${size}" rx="${radius}" fill="${fill}" opacity="${opacity}" transform="rotate(${rotate} ${x + size / 2} ${y + size / 2})"/>`
    }

    return `<path d="M${x + size / 2} ${y} L${x + size} ${y + size / 2} L${x + size / 2} ${y + size} L${x} ${y + size / 2} Z" fill="${fill}" opacity="${opacity}" transform="rotate(${rotate} ${x + size / 2} ${y + size / 2})"/>`
  }

  const shapes = `
    ${shape(shapeTypeA, x1, y1, s1, r1, 'white', '0.86', rotateA)}
    ${shape(shapeTypeB, x2, y2, s2, r2, accent, '0.82', rotateB)}
    ${shape(shapeTypeC, x3, y3, s3, r3, 'white', '0.22', 0)}
    <path d="M${lineX1} ${lineY1} C${Math.round(lineX1 + 12)} ${Math.round(lineY1 - 18)} ${Math.round(lineX2 - 10)} ${Math.round(lineY2 + 18)} ${lineX2} ${lineY2}" stroke="${ring}" stroke-width="${strokeWidth}" stroke-linecap="round" fill="none" opacity="0.45"/>
  `

  const svg = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
      <defs>
        <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="${fillA}"/>
          <stop offset="100%" stop-color="${fillB}"/>
        </linearGradient>
      </defs>
      <rect width="100" height="100" rx="32" fill="url(%23g)"/>
      <circle cx="78" cy="22" r="12" fill="${ring}" opacity="0.16"/>
      <circle cx="21" cy="81" r="17" fill="white" opacity="0.08"/>
      ${shapes}
    </svg>
  `

  return `data:image/svg+xml;charset=UTF-8,${svg.trim()}`
}
