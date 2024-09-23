import React from 'react';
import Navbar from '@/components/layout/Navbar';
import Footer from '@/components/layout/Footer';

const layout = ({ children }: { children: React.ReactNode }) => {
    return (
        <>
            <Navbar />
            <div className='w-full max-w-screen-xl mx-auto py-1'>
                {children}
            </div>
            <Footer />
        </>
    )
}

export default layout