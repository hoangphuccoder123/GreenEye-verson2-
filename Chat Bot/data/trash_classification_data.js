// Dữ liệu toàn diện về phân loại rác và xử lý rác thải
const trashClassificationData = {
    // Các loại rác chính
    trashCategories: {
        organic: {
            name: "Rác hữu cơ",
            description: "Rác có nguồn gốc từ thực vật, động vật, có thể phân hủy sinh học",
            color: "#4CAF50",
            icon: "🍃",
            examples: [
                "Vỏ trái cây (chuối, cam, táo, xoài)",
                "Thức ăn thừa (cơm, thịt, cá, rau củ)",
                "Lá cây, cành cây nhỏ",
                "Vỏ trứng",
                "Bã cà phê, trà",
                "Xương cá, xương gà nhỏ",
                "Hoa héo, lá khô",
                "Rau củ hỏng",
                "Bánh mì cũ",
                "Phân động vật"
            ],
            processingTime: "2-6 tháng",
            benefits: [
                "Tạo phân compost tự nhiên",
                "Cải thiện độ màu mỡ đất",
                "Giảm khí thải nhà kính",
                "Tiết kiệm chi phí xử lý rác"
            ]
        },
        recyclable: {
            name: "Rác tái chế",
            description: "Rác có thể tái chế thành sản phẩm mới",
            color: "#2196F3",
            icon: "♻️",
            subcategories: {
                plastic: {
                    name: "Nhựa",
                    types: {
                        pet: {
                            code: "PET/PETE (1)",
                            examples: ["Chai nước suối", "Chai nước ngọt", "Hộp đựng thực phẩm trong suốt"],
                            recyclable: true,
                            note: "Dễ tái chế nhất"
                        },
                        hdpe: {
                            code: "HDPE (2)",
                            examples: ["Chai sữa", "Chai dầu gội", "Túi nhựa dày"],
                            recyclable: true,
                            note: "Tái chế tốt"
                        },
                        pvc: {
                            code: "PVC (3)",
                            examples: ["Ống nước", "Màng bọc thực phẩm", "Thẻ tín dụng"],
                            recyclable: false,
                            note: "Độc hại khi đốt"
                        },
                        ldpe: {
                            code: "LDPE (4)",
                            examples: ["Túi nilon mỏng", "Màng bọc", "Nắp chai mềm"],
                            recyclable: "limited",
                            note: "Khó tái chế"
                        },
                        pp: {
                            code: "PP (5)",
                            examples: ["Hộp yogurt", "Ống hút", "Nắp chai cứng"],
                            recyclable: true,
                            note: "Tái chế tốt"
                        },
                        ps: {
                            code: "PS (6)",
                            examples: ["Hộp xốp", "Cốc nhựa trong", "Đồ chơi nhựa"],
                            recyclable: false,
                            note: "Khó tái chế, có thể độc hại"
                        },
                        other: {
                            code: "Other (7)",
                            examples: ["Nhựa sinh học", "Nhựa composite", "Nhựa đa lớp"],
                            recyclable: false,
                            note: "Không tái chế được"
                        }
                    }
                },
                paper: {
                    name: "Giấy",
                    examples: [
                        "Báo cũ, tạp chí",
                        "Giấy văn phòng",
                        "Sách vở cũ",
                        "Hộp carton",
                        "Túi giấy",
                        "Giấy bao bì"
                    ],
                    excluded: [
                        "Giấy bóng, giấy ảnh",
                        "Giấy dính băng keo",
                        "Giấy ướt, dơ",
                        "Giấy vệ sinh đã dùng"
                    ]
                },
                metal: {
                    name: "Kim loại",
                    examples: [
                        "Lon nhôm (nước ngọt, bia)",
                        "Lon thiếc (thức ăn đóng hộp)",
                        "Dây điện cũ",
                        "Đinh, vít kim loại",
                        "Nồi chảo cũ",
                        "Khung xe đạp cũ"
                    ]
                },
                glass: {
                    name: "Thủy tinh",
                    examples: [
                        "Chai lọ thủy tinh",
                        "Cốc thủy tinh",
                        "Bóng đèn sợi đốt",
                        "Gương cũ"
                    ],
                    excluded: [
                        "Thủy tinh cường lực",
                        "Đèn huỳnh quang",
                        "Thủy tinh ô tô"
                    ]
                }
            }
        },
        hazardous: {
            name: "Rác độc hại",
            description: "Rác chứa chất độc hại, cần xử lý đặc biệt",
            color: "#F44336",
            icon: "☠️",
            examples: [
                "Pin, ắc quy",
                "Đèn huỳnh quang, đèn compact",
                "Thuốc trừ sâu, hóa chất",
                "Sơn, keo dán",
                "Dung môi, axit",
                "Nhiệt kế thủy ngân",
                "Thiết bị điện tử (TV, máy tính)",
                "Thuốc hết hạn",
                "X-quang phim",
                "Lốp xe cũ"
            ],
            dangers: [
                "Gây ô nhiễm đất, nước",
                "Độc hại với sức khỏe",
                "Gây cháy nổ",
                "Ảnh hưởng hệ sinh thái"
            ],
            specialHandling: [
                "Thu gom riêng biệt",
                "Không được vứt chung",
                "Giao cho cơ sở xử lý chuyên biệt",
                "Đeo bảo hộ khi xử lý"
            ]
        },
        nonRecyclable: {
            name: "Rác không tái chế",
            description: "Rác không thể tái chế, cần xử lý bằng cách khác",
            color: "#9E9E9E",
            icon: "🗑️",
            examples: [
                "Tã lót trẻ em",
                "Băng vệ sinh",
                "Bao cao su",
                "Bóng bay cao su",
                "Kính cường lực",
                "Gương soi",
                "Bóng đèn LED hỏng",
                "Ceramic, sứ",
                "Giấy carbon",
                "Băng keo trong"
            ]
        }
    },

    // Luật và quy định về phân loại rác
    wasteManagementLaws: {
        vietnam: {
            lawName: "Luật Bảo vệ môi trường 2020",
            keyPoints: [
                "Phân loại rác tại nguồn là bắt buộc từ năm 2025",
                "Phạt tiền từ 500.000 - 1.000.000 VNĐ cho cá nhân không phân loại",
                "Phạt từ 10-20 triệu VNĐ cho doanh nghiệp vi phạm",
                "Khuyến khích giảm thiểu rác thải nhựa",
                "Tăng cường tái chế và tái sử dụng"
            ],
            requirements: [
                "Phân loại tối thiểu 3 loại: hữu cơ, tái chế, còn lại",
                "Sử dụng túi/thùng màu khác nhau",
                "Thu gom theo lịch riêng biệt",
                "Giáo dục cộng đồng về phân loại"
            ]
        },
        international: {
            japan: {
                country: "Nhật Bản",
                system: "Phân loại cực kỳ chi tiết (7-10 loại)",
                schedule: "Thu gom theo lịch nghiêm ngặt",
                penalty: "Phạt nặng, có thể trục xuất người nước ngoài"
            },
            germany: {
                country: "Đức",
                system: "Hệ thống Dual System - phân loại 5 loại chính",
                incentive: "Hoàn tiền cho chai lọ tái chế",
                achievement: "Tỷ lệ tái chế đạt 67%"
            },
            singapore: {
                country: "Singapore",
                system: "Phân loại 4 loại, ứng dụng AI trong thu gom",
                goal: "Zero Waste Nation - không chôn lấp từ 2030",
                technology: "Sử dụng robot phân loại"
            }
        }
    },

    // Cách xử lý rác thải đúng cách
    properWasteProcessing: {
        organicWaste: {
            method: "Ủ phân compost",
            steps: [
                "Cho rác hữu cơ vào thùng/hố ủ phân",
                "Trộn với lá khô theo tỷ lệ 3:1",
                "Tưới nước vừa đủ để giữ ẩm",
                "Trở đều 1-2 tuần/lần",
                "Thu hoạch compost sau 2-6 tháng"
            ],
            benefits: [
                "Tạo phân bón hữu cơ",
                "Giảm 30% lượng rác thải",
                "Cải thiện cấu trúc đất",
                "Tiết kiệm tiền mua phân bón"
            ],
            tips: [
                "Cắt nhỏ rác hữu cơ để ủ nhanh hơn",
                "Tránh thịt cá để không thu hút ruồi muỗi",
                "Kiểm soát độ ẩm 50-60%",
                "Nhiệt độ ủ lý tưởng 50-65°C"
            ]
        },
        recyclableWaste: {
            preparation: [
                "Rửa sạch bao bì trước khi bỏ",
                "Tháo nắp, nhãn dán nếu có thể",
                "Phân loại theo chất liệu",
                "Để khô trước khi thu gom"
            ],
            process: [
                "Thu gom và vận chuyển đến nhà máy",
                "Phân loại chi tiết bằng máy móc",
                "Nghiền nhỏ, rửa sạch",
                "Nấu chảy/xử lý thành nguyên liệu mới",
                "Sản xuất sản phẩm mới"
            ]
        },
        hazardousWaste: {
            handling: [
                "Đeo găng tay, khẩu trang khi xử lý",
                "Bọc kỹ, dán nhãn cảnh báo",
                "Bảo quản nơi khô ráo, thoáng mát",
                "Không được vứt chung với rác thường"
            ],
            disposal: [
                "Liên hệ đơn vị thu gom chuyên biệt",
                "Mang đến điểm thu gom của chính quyền",
                "Sử dụng dịch vụ thu gom nguy hại",
                "Xử lý tại cơ sở có giấy phép"
            ]
        }
    },

    // 3R - Reduce, Reuse, Recycle
    sustainabilityPrinciples: {
        reduce: {
            name: "Giảm thiểu (Reduce)",
            priority: 1,
            actions: [
                "Mua sắm có kế hoạch, tránh dư thừa",
                "Chọn sản phẩm có bao bì tối giản",
                "Sử dụng túi vải thay túi nilon",
                "In hai mặt giấy",
                "Tắt điện khi không dùng",
                "Sử dụng cốc, đũa cá nhân",
                "Mua sản phẩm có độ bền cao"
            ]
        },
        reuse: {
            name: "Tái sử dụng (Reuse)",
            priority: 2,
            actions: [
                "Tái sử dụng chai lọ làm chậu trồng cây",
                "Dùng mặt sau của giấy đã in",
                "Biến hộp carton thành đồ chơi",
                "Sửa chữa đồ đạc thay vì vứt bỏ",
                "Tặng, bán đồ cũ còn dùng được",
                "Dùng túi nilon nhiều lần",
                "Tận dụng vỏ hộp làm hộp đựng"
            ]
        },
        recycle: {
            name: "Tái chế (Recycle)",
            priority: 3,
            actions: [
                "Phân loại rác đúng cách",
                "Làm sạch bao bì trước khi tái chế",
                "Tham gia chương trình thu gom tái chế",
                "Ủng hộ sản phẩm từ vật liệu tái chế",
                "Giáo dục người khác về tái chế"
            ]
        }
    },

    // Ứng dụng công nghệ trong phân loại rác
    technologyApplications: {
        aiClassification: {
            name: "Phân loại bằng AI",
            description: "Sử dụng trí tuệ nhân tạo để nhận diện và phân loại rác",
            advantages: [
                "Độ chính xác cao (>95%)",
                "Xử lý nhanh, liên tục",
                "Giảm sai sót do con người",
                "Học hỏi và cải thiện liên tục"
            ],
            implementation: [
                "Camera chụp ảnh rác thải",
                "AI phân tích hình ảnh",
                "Xác định loại rác",
                "Đưa ra hướng dẫn phân loại"
            ]
        },
        smartBins: {
            name: "Thùng rác thông minh",
            features: [
                "Cảm biến đầy/rỗng",
                "Tự động mở nắp",
                "Nén rác tự động",
                "Kết nối IoT",
                "Thông báo khi cần thu gom"
            ]
        },
        mobileApps: {
            name: "Ứng dụng di động",
            functions: [
                "Hướng dẫn phân loại",
                "Lịch thu gom rác",
                "Điểm thưởng tái chế",
                "Bản đồ điểm thu gom",
                "Chia sẻ kinh nghiệm"
            ]
        }
    },

    // Thống kê và số liệu
    statistics: {
        vietnam: {
            totalWaste: "27.8 triệu tấn/năm (2020)",
            wastePerCapita: "0.84 kg/người/ngày",
            recyclingRate: "6-8%",
            organicWaste: "65%",
            plasticWaste: "8-12%",
            paperWaste: "5-7%"
        },
        global: {
            totalWaste: "2.01 tỷ tấn/năm",
            projection2050: "3.4 tỷ tấn/năm",
            topRecyclers: [
                "Đức (67%)",
                "Áo (63%)",
                "Hàn Quốc (59%)",
                "Wales (56%)",
                "Thụy Sĩ (53%)"
            ]
        }
    },

    // Tác hại của việc không phân loại rác
    environmentalImpacts: {
        soilPollution: {
            name: "Ô nhiễm đất",
            causes: [
                "Chôn lấp rác không phân loại",
                "Rò rỉ hóa chất từ rác độc hại",
                "Phân hủy không hoàn toàn"
            ],
            effects: [
                "Giảm độ màu mỡ đất",
                "Ảnh hưởng cây trồng",
                "Ô nhiễm nước ngầm"
            ]
        },
        waterPollution: {
            name: "Ô nhiễm nước",
            causes: [
                "Nước rác thải thấm xuống",
                "Vứt rác xuống sông, biển",
                "Hóa chất từ rác độc hại"
            ],
            effects: [
                "Nước sinh hoạt bị ô nhiễm",
                "Cá chết hàng loạt",
                "Ảnh hưởng hệ sinh thái nước"
            ]
        },
        airPollution: {
            name: "Ô nhiễm không khí",
            causes: [
                "Đốt rác thải bừa bãi",
                "Khí thải từ bãi rác",
                "Phân hủy yếm khí"
            ],
            effects: [
                "Khí nhà kính tăng",
                "Bệnh về đường hô hấp",
                "Mùi hôi thối"
            ]
        }
    },

    // Các sáng kiến xanh
    greenInitiatives: {
        community: [
            "Ngày chủ nhật xanh - dọn dẹp môi trường",
            "Trao đổi đồ cũ trong cộng đồng",
            "Chợ phiên đồ tái chế",
            "Nhóm ủ phân compost cộng đồng",
            "Chiến dịch không dùng túi nilon"
        ],
        school: [
            "Giáo dục môi trường từ tiểu học",
            "Cuộc thi sáng tạo từ rác thải",
            "Mô hình trường học xanh",
            "Câu lạc bộ bảo vệ môi trường",
            "Ngày không rác thải nhựa"
        ],
        corporate: [
            "Chính sách giảm thiểu bao bì",
            "Chương trình thu hồi sản phẩm",
            "Văn phòng không giấy",
            "Khuyến khích nhân viên đi xe đạp",
            "Đầu tư công nghệ xanh"
        ]
    },

    // Mẹo và thủ thuật phân loại rác
    practicalTips: {
        kitchen: [
            "Đặt nhiều thùng rác nhỏ cho từng loại",
            "Dùng túi giấy cho rác hữu cơ",
            "Rửa sơ qua đồ đóng hộp trước khi bỏ",
            "Tách nắp chai ra khỏi thân chai",
            "Không bỏ dầu mỡ xuống bồn rửa"
        ],
        bathroom: [
            "Ống thuốc đánh răng cắt đôi để rửa sạch",
            "Chai shampoo rửa sạch trước khi bỏ",
            "Bao bì mỹ phẩm phân loại theo chất liệu",
            "Bông tăm, bông gòn vào rác không tái chế"
        ],
        office: [
            "Hộp mực in cũ giao lại nhà cung cấp",
            "Giấy một mặt dùng làm giấy nháp",
            "Pin cũ thu gom riêng",
            "Thiết bị điện tử cũ đưa đến điểm thu gom"
        ]
    }
};

// Hàm tiện ích để lấy thông tin phân loại
const wasteClassificationUtils = {
    // Nhận diện loại rác từ tên
    identifyWasteType: function(itemName) {
        const item = itemName.toLowerCase();
        
        for (const [categoryKey, categoryData] of Object.entries(trashClassificationData.trashCategories)) {
            if (categoryData.examples) {
                for (const example of categoryData.examples) {
                    if (example.toLowerCase().includes(item) || item.includes(example.toLowerCase())) {
                        return {
                            category: categoryKey,
                            name: categoryData.name,
                            color: categoryData.color,
                            icon: categoryData.icon
                        };
                    }
                }
            }
            
            // Kiểm tra subcategories
            if (categoryData.subcategories) {
                for (const [subKey, subData] of Object.entries(categoryData.subcategories)) {
                    if (subData.examples) {
                        for (const example of subData.examples) {
                            if (example.toLowerCase().includes(item) || item.includes(example.toLowerCase())) {
                                return {
                                    category: categoryKey,
                                    subcategory: subKey,
                                    name: categoryData.name,
                                    subname: subData.name,
                                    color: categoryData.color,
                                    icon: categoryData.icon
                                };
                            }
                        }
                    }
                }
            }
        }
        
        return null;
    },

    // Lấy hướng dẫn xử lý cho loại rác
    getProcessingGuide: function(category) {
        const guides = {
            organic: trashClassificationData.properWasteProcessing.organicWaste,
            recyclable: trashClassificationData.properWasteProcessing.recyclableWaste,
            hazardous: trashClassificationData.properWasteProcessing.hazardousWaste,
            nonRecyclable: {
                method: "Chôn lấp hoặc đốt an toàn",
                note: "Tìm cách thay thế bằng sản phẩm thân thiện môi trường"
            }
        };
        
        return guides[category] || null;
    },

    // Lấy màu thùng rác theo loại
    getBinColor: function(category) {
        const colors = {
            organic: "green",
            recyclable: "blue", 
            hazardous: "red",
            nonRecyclable: "gray"
        };
        
        return colors[category] || "gray";
    },

    // Tính điểm môi trường dựa trên hành động
    calculateEcoPoints: function(action, quantity = 1) {
        const points = {
            compost_organic: 10,
            recycle_plastic: 5,
            recycle_paper: 3,
            proper_hazardous_disposal: 15,
            reduce_usage: 8,
            reuse_item: 6
        };
        
        return (points[action] || 0) * quantity;
    },

    // Lấy tip ngẫu nhiên
    getRandomTip: function() {
        const allTips = [
            ...trashClassificationData.practicalTips.kitchen,
            ...trashClassificationData.practicalTips.bathroom,
            ...trashClassificationData.practicalTips.office
        ];
        
        return allTips[Math.floor(Math.random() * allTips.length)];
    },

    // Kiểm tra xem một vật phẩm có thể compost được không
    isCompostable: function(item) {
        const compostableItems = trashClassificationData.trashCategories.organic.examples;
        const itemLower = item.toLowerCase();
        
        return compostableItems.some(example => 
            example.toLowerCase().includes(itemLower) || 
            itemLower.includes(example.toLowerCase())
        );
    },

    // Lấy thống kê tổng quan
    getStatistics: function() {
        return {
            vietnam: trashClassificationData.statistics.vietnam,
            global: trashClassificationData.statistics.global,
            environmental_impacts: trashClassificationData.environmentalImpacts
        };
    }
};

// Export để sử dụng trong các file khác
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        trashClassificationData,
        wasteClassificationUtils
    };
}

// Ví dụ sử dụng
console.log("=== VÍ DỤ SỬ DỤNG DỮLIỆU PHÂN LOẠI RÁC ===");
console.log("\n1. Nhận diện loại rác:");
console.log(wasteClassificationUtils.identifyWasteType("vỏ chuối"));
console.log(wasteClassificationUtils.identifyWasteType("chai nhựa"));

console.log("\n2. Hướng dẫn xử lý rác hữu cơ:");
console.log(wasteClassificationUtils.getProcessingGuide("organic"));

console.log("\n3. Tip ngẫu nhiên:");
console.log(wasteClassificationUtils.getRandomTip());

console.log("\n4. Kiểm tra khả năng compost:");
console.log("Vỏ trứng có thể compost:", wasteClassificationUtils.isCompostable("vỏ trứng"));
console.log("Túi nilon có thể compost:", wasteClassificationUtils.isCompostable("túi nilon"));

console.log("\n5. Tính điểm môi trường:");
console.log("Ủ phân 1kg rác hữu cơ:", wasteClassificationUtils.calculateEcoPoints("compost_organic", 1), "điểm");
