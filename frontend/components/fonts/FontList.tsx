'use client'

import React, { useEffect, useState, useMemo } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import { Space, Table } from 'antd';
import Swal from 'sweetalert2';


import { formUrlQuery, fontDelete, getFonts } from "@/lib/actions";

const { Column } = Table;

interface DataType {
    key: React.Key;
    title: string;
    font_url: string;
}

interface FontProps {
    className?: string;
    fonts?: any;
}

const FontList = React.memo(({ className, fonts }: FontProps) => {

    const searchPageParams = useSearchParams();
    const router = useRouter();
    const [loading, setLoading] = useState(true);
    const [data, setData] = useState<DataType[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [pageSize, setPageSize] = useState(10);
    const [total, setTotal] = useState(0);

    const loadedFonts = useMemo<Set<string>>(() => new Set<string>(), []);

    const slug = (title: string) => {
        return title
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    };

    const addFontFace = (fontTitle: string, fontUrl: string) => {
        if (loadedFonts.has(fontTitle)) {
            return;
        }
        const fontStyle = document.createElement('style');
        fontStyle.id = `font-${fontTitle}`;
        fontStyle.innerHTML = `
          @font-face {
            font-family: '${fontTitle}';
            src: local('${fontTitle}'), url('${fontUrl}') format("truetype");
            font-weight: normal;
            font-style: normal;
          }
        `;
        document.head.appendChild(fontStyle);
        loadedFonts.add(fontTitle);
    };

    useEffect(() => {
        setLoading(true);

        if (fonts.data) {
            const fontList: DataType[] = fonts.data.map((font: any) => {
                const fontSlug = slug(font.title);
                addFontFace(fontSlug, font.font_url);
                return {
                    key: font.id,
                    title: font.title,
                    font_url: font.font_url,
                };
            });

            setCurrentPage(fonts.current_page);
            setPageSize(fonts.per_page);
            setTotal(fonts.total);
            setData(fontList);
        }

        const delay = setTimeout(() => {
            setLoading(false);
        }, 1500);
        return () => clearTimeout(delay);
    }, [fonts]);

    const onPaginationChange = (page: number, pageSize?: number) => {
        setCurrentPage(page);
        if (pageSize) setPageSize(pageSize);

        const newUrl = formUrlQuery({
            params: searchPageParams.toString(),
            key: "page",
            value: page.toString(),
        });
        router.push(newUrl, { scroll: false });
    };

    const handleDelete = (id: number) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then(async (result) => {
            if (result.isConfirmed) {
                const font_delete = await fontDelete(id);
                if (font_delete.success) {
                    setLoading(true); // Show loading while fetching updated data
                    Swal.fire({
                        title: "Deleted!",
                        text: font_delete.message,
                        icon: "success"
                    });

                    // Fetch the updated fonts after deletion
                    const fonts = await getFonts({
                        page: currentPage
                    });

                    if (fonts?.data) {
                        const fontList: DataType[] = fonts.data.map((font: any) => {
                            const fontSlug = slug(font.title);
                            addFontFace(fontSlug, font.font_url);
                            return {
                                key: font.id,
                                title: font.title,
                                font_url: font.font_url,
                            };
                        });

                        setCurrentPage(fonts.current_page);
                        setPageSize(fonts.per_page);
                        setTotal(fonts.total);
                        setData(fontList);
                    }
                    setLoading(false); // Hide loading after fetching
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: font_delete.message,
                        icon: "error"
                    });
                }
            }
        });
    };

    return (
        <div className={`${className}`}>
            <Table<DataType>
                loading={loading}
                dataSource={data}
                pagination={{
                    current: currentPage,
                    pageSize: pageSize,
                    total: total,
                    onChange: onPaginationChange,
                    position: ['bottomRight'],
                }}
            >
                <Column title="Font Name" dataIndex="title" key="title" />
                <Column
                    title="Preview"
                    dataIndex="font_url"
                    key="font_url"
                    render={(_: string, record: DataType) => (
                        <span style={{ fontFamily: slug(record.title) }} className="">
                            Example Style
                        </span>
                    )}
                />
                <Column
                    title="Action"
                    key="action"
                    render={(_: any, record: DataType) => (
                        <Space size="middle" className="flex justify-end">
                            <span
                                onClick={() => handleDelete(Number(record.key))}
                                className="text-red-600 hover:text-red-700 text-base font-normal cursor-pointer"
                            >
                                Delete
                            </span>
                        </Space>
                    )}
                />
            </Table>
        </div>
    );
});

export default FontList;
