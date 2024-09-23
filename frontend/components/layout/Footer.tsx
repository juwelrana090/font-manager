import Link from 'next/link'
import React from 'react'

const Footer = () => {
    return (
        <footer className='w-full text-white font-serif flex-between text-base gap-y-10 border-t border-gray-700/55 px-20 py-12 max-md:flex-col'>
            <p>Copyright 2024 CodingZoneBD | All Right Reserved</p>
            <div className='flex gap-x-9'>
                <Link href={`/terms-of-use`}>
                    Terms & Conditions
                </Link>
                <Link href={`/privacy-policy`}>
                    Privacy Policy
                </Link>
            </div>
        </footer>
    )
}

export default Footer