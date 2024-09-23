/** @type {import('next').NextConfig} */
const NEXT_PUBLIC_BASE_URL = process.env.NEXT_PUBLIC_BASE_URL;
const NEXT_PUBLIC_BACKEND_URL = process.env.NEXT_PUBLIC_BACKEND_URL;
const NEXT_PUBLIC_API_URL = process.env.NEXT_PUBLIC_API_URL;

const nextConfig = {
  env: {
    NEXT_PUBLIC_BASE_URL: NEXT_PUBLIC_BASE_URL,
    NEXT_PUBLIC_BACKEND_URL: NEXT_PUBLIC_BACKEND_URL,
    NEXT_PUBLIC_API_URL: NEXT_PUBLIC_API_URL,
  },
  images: {
    remotePatterns: [
      { hostname: '*' },
      { hostname: 'localhost' },
      { hostname: 'localhost:3000' },
      { hostname: 'fonts.codingzonebd.com' },
    ]
  },
};

export default nextConfig;
