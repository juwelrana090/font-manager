'use client'

import React, { useEffect, useState } from 'react';
import { Space, Table } from 'antd';

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

const FontList = ({ className, fonts }: FontProps) => {
    const [loading, setLoading] = useState(true);
    const [data, setData] = useState<DataType[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [pageSize, setPageSize] = useState(10);
    const [total, setTotal] = useState(0);


    const slug = (title: string) => {
        return title
            .toLowerCase()                     // Convert to lowercase
            .trim()                            // Remove leading/trailing spaces
            .replace(/[^a-z0-9 -]/g, '') 
            .replace(/\s+/g, '-') 
            .replace(/-+/g, '-')
    }
    
    const addFontFace = (fontTitle: string, fontUrl: string) => {
        if (document.querySelector(`#font-${fontTitle}`)) {
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
    };


    useEffect(() => {
        setLoading(true);

        let fontList: DataType[] = [];

        console.log('fonts', fonts);


        if (fonts.data) {
            fonts.data.forEach((font: any) => {
                const fontSlug = slug(font.title);
                fontList.push({
                    key: font.id,
                    title: font.title,
                    font_url: font.font_url,
                });

                addFontFace(fontSlug, font.font_url);
            });
        }
        setCurrentPage(fonts.current_page);
        setPageSize(fonts.per_page);
        setTotal(fonts.total);
        setData(fontList);

        const delay = setTimeout(() => {
            setLoading(false);
        }, 1500);
        return () => clearTimeout(delay);
    }, [fonts]);

    const onPaginationChange = (page: number, pageSize?: number) => {
        setCurrentPage(page);
        if (pageSize) setPageSize(pageSize);
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
                            <span className="text-red-600 hover:text-red-700 text-base font-normal cursor-pointer">
                                Delete
                            </span>
                        </Space>
                    )}
                />
            </Table>
        </div>
    );
};

export default FontList;
