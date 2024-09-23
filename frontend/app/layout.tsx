import type { Metadata } from "next";
import { AntdRegistry } from '@ant-design/nextjs-registry';
import "./globals.css";

export const metadata: Metadata = {
  title: "Font Manager",
  description: "Font Manager helps organize, preview, activate, and manage fonts efficiently for designers and developers, ensuring a streamlined workflow.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body
        className="w-screen h-screen"
      >
        <AntdRegistry>{children}</AntdRegistry>
      </body>
    </html>
  );
}
