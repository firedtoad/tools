/*
 * Func.h
 *  Created on: 2018年11月26日
 *      Author: dietoad@gmail.com
 */

#ifndef FUNC_H_
#define FUNC_H_
#include <string>
#include <cstring>
#include <sstream>
#include <vector>
#include <map>
#include <set>
#include <list>
#include <time.h>
#include <unordered_map>
#include <unordered_set>

namespace Func
{
    template<typename K, typename V>
    std::unordered_map<K, V> page(std::unordered_map<K, V> &mp, size_t pnum, uint32_t page_size)
    {
        std::unordered_map<K, V> result;
        size_t total = mp.size() / page_size;
        if (pnum <= total)
        {
            uint32_t begin = pnum * page_size;
            auto it = mp.begin();
            uint32_t skip = 0;
            uint32_t need = 0;
            while (skip++ < begin)
            {
                it++;
            }
            for (; it != mp.end() && need++ <= page_size; ++it)
            {
                result[it->first] = it->second;
            }
        }
        return result;
    }

    template<typename V>
    std::vector<V> page(std::vector<V> &mp, size_t pnum, uint32_t page_size)
    {
        std::vector<V> result;
        size_t total = mp.size() / page_size;
        if (pnum <= total)
        {
            uint32_t begin = pnum * page_size;
            auto it = mp.begin();
            uint32_t skip = 0;
            uint32_t need = 0;
            while (skip++ < begin)
            {
                it++;
            }
            for (; it != mp.end() && need < page_size; need++, ++it)
            {
                result.push_back(*it);
            }
        }
        return result;
    }

    template<typename V>
    std::vector<V> pageForRank(std::vector<V> &vec, uint32_t from, uint32_t sz)
    {
        std::vector<V> result;
        uint32_t skip = 0;
        uint32_t need = 0;
        if (from > vec.size())
        {
            from = vec.size();
        }
        auto it = vec.begin();
        while (skip++ < from)
        {
            it++;
        }
        for (; it != vec.end() && need < sz; need++, ++it)
        {
            result.push_back(*it);
        }
        return result;
    }

    template<typename V>
    std::vector<V> page(std::list<V> &mp, size_t pnum, uint32_t page_size)
    {
        std::vector<V> result;
        size_t total = mp.size() / page_size;
        if (pnum <= total)
        {
            uint32_t begin = pnum * page_size;
            auto it = mp.begin();
            uint32_t skip = 0;
            uint32_t need = 0;
            while (skip++ < begin)
            {
                it++;
            }
            for (; it != mp.end() && need < page_size; need++, ++it)
            {
                result.push_back(*it);
            }
        }
        return result;
    }

    template<typename K, typename V>
    void serMap(const std::map<K, V> &mp, std::string &buf)
    {
        uint32_t sz = mp.size();
        buf.append((const char*) &sz, sizeof(sz));
        for (auto &it : mp)
        {
            buf.append((const char*) &it.first, sizeof(it.first));
            buf.append((const char*) &it.second, sizeof(it.second));
        }
    }

    template<typename K, typename V>
    uint32_t unserMap(std::map<K, V> &mp, const uint8_t* pData)
    {
        uint32_t sz = *(uint32_t*) pData;
        uint32_t ksz = sizeof(K);
        uint32_t vsz = sizeof(V);
        uint32_t ret = 0;
        ret += sizeof(sz);
        uint32_t kvsz = ksz + vsz;
        const uint8_t* p = &pData[sizeof(sz)];

        for (uint32_t i = 0; i < sz; ++i)
        {
            mp[*(K*) &p[i * kvsz]] = *(V*) &p[i * kvsz + ksz];
        }
        ret += sz * (kvsz);
        return ret;
    }

    template<typename V>
    void serUnSet(const std::unordered_set<V> &us, std::string &buf)
    {
        uint32_t sz = us.size();
        buf.append((const char*) &sz, sizeof(sz));
        for (auto &it : us)
        {
            buf.append((const char*) &it, sizeof(it));
        }
    }

    template<typename V>
    uint32_t unserUnSet(std::unordered_set<V> &us, const uint8_t *pData)
    {
        uint32_t sz = *(uint32_t*) pData;
        uint32_t vsz = sizeof(V);
        uint32_t ret = 0;
        ret += sizeof(sz);
        const uint8_t* p = &pData[vsz];

        for (uint32_t i = 0; i < sz; ++i)
        {
            us.insert(*(V*) &p[i * (vsz)]);
        }
        ret += sz * vsz;
        return ret;
    }

    template<typename K, typename V>
    void serUnMap(const std::unordered_map<K, V> &mp, std::string &buf)
    {
        uint32_t sz = mp.size();
        buf.append((const char*) &sz, sizeof(sz));
        for (auto &it : mp)
        {
            buf.append((const char*) &it.first, sizeof(it.first));
            buf.append((const char*) &it.second, sizeof(it.second));
        }
    }

    template<typename K, typename V>
    uint32_t unserUnMap(std::unordered_map<K, V> &mp, const uint8_t *pData)
    {
        uint32_t sz = *(uint32_t*) pData;
        uint32_t ksz = sizeof(K);
        uint32_t vsz = sizeof(V);
        uint32_t ret = 0;
        ret += sizeof(sz);
        uint32_t kvsz = ksz + vsz;
        const uint8_t* p = &pData[sizeof(sz)];
        for (uint32_t i = 0; i < sz; ++i)
        {
            mp[*(K*) &p[i * kvsz]] = *(V*) &p[(i * kvsz) + ksz];
        }
        ret += sz * (kvsz);
        return ret;
    }

    template<typename V>
    void serSet(const std::set<V> &st, std::string &buf)
    {
        uint32_t sz = st.size();
        buf.append((const char*) &sz, sizeof(sz));
        for (auto &it : st)
        {
            buf.append((const char*) &it, sizeof(it));
        }
    }

    template<typename V>
    uint32_t unserSet(std::unordered_set<V> &us, const uint8_t* pData)
    {
        uint32_t sz = *(uint32_t*) pData;
        uint32_t vsz = sizeof(V);
        uint32_t ret = 0;
        ret += sizeof(sz);
        const uint8_t* p = &pData[sizeof(sz)];

        for (uint32_t i = 0; i < sz; ++i)
        {
            us.insert(*(V*) &p[i * (vsz)]);
        }
        ret += sz * vsz;
        return ret;
    }

    template<typename V>
    void serVec(const std::vector<V> &sv, std::string &buf)
    {
        uint32_t sz = sv.size();
        buf.append((const char*) &sz, sizeof(sz));
        for (auto &it : sv)
        {
            buf.append((const char*) &it, sizeof(it));
        }
    }

    template<typename V>
    void serslist(const std::list<V> &sv, std::string &buf)
    {
        uint32_t sz = sv.size();
        buf.append((const char*) &sz, sizeof(sz));
        for (auto &it : sv)
        {
            buf.append((const char*) &it, sizeof(it));
        }
    }

    template<typename V>
    uint32_t unserlist(std::list<V> &vc, const uint8_t* pData)
    {
        uint32_t sz = *(uint32_t*) pData;
        uint32_t vsz = sizeof(V);
        uint32_t ret = 0;
        ret += sizeof(sz);
        const uint8_t* p = &pData[sizeof(sz)];
        for (uint32_t i = 0; i < sz; ++i)
        {
            vc.push_back(*(V*) &p[i * (vsz)]);
        }
        ret += sz * vsz;
        return ret;
    }

    template<typename V>
    uint32_t unserVec(std::vector<V> &vc, const uint8_t* pData)
    {
        uint32_t sz = *(uint32_t*) pData;
        uint32_t vsz = sizeof(V);
        uint32_t ret = 0;
        ret += sizeof(sz);
        const uint8_t* p = &pData[sizeof(sz)];
        for (uint32_t i = 0; i < sz; ++i)
        {
            vc.push_back(*(V*) &p[i * (vsz)]);
        }
        ret += sz * vsz;
        return ret;
    }

    template<typename V>
    uint32_t unserVec(std::vector<V> &vc, uint16_t len, const uint8_t* pData)
    {
        uint32_t vsz = sizeof(V);
        uint32_t ret = 0;
        const uint8_t* p = &pData[ret];
        for (uint32_t i = 0; i < len; ++i)
        {
            vc.push_back(*(V*) &p[i * (vsz)]);
        }
        ret += len * vsz;
        return ret;
    }

    template<typename SZ>
    void unserKeyValues(std::vector<std::string> &keys, std::vector<std::string> &vals, std::string &buf, uint32_t size, SZ ksize, SZ vsize)
    {
        if (size > 0)
        {
            SZ kvsize = ksize + vsize;
            uint32_t index = sizeof(size);
            for (int i = 0; i < size; ++i)
            {
                std::string k, v;
                k.assign(buf, index, ksize);
                v.assign(buf, index + ksize, vsize);
                index += kvsize;
                keys.push_back(k);
                vals.push_back(v);
            }
        }
    }
    template<typename SZ>
    void unserValues(std::vector<std::string> &vals, std::string &buf, uint32_t size, SZ vsize)
    {
        if (size > 0)
        {
            SZ kvsize = vsize;
            uint32_t index = sizeof(size);
            for (int i = 0; i < size; ++i)
            {
                std::string k, v;
                v.assign(buf, index, vsize);
                index += kvsize;
                vals.push_back(v);
            }
        }
    }

    //数字转字符串
    template<typename T>
    inline std::string Digit2Str(T src)
    {
        std::stringstream ss;
        ss << src;
        return ss.str();
    }
    //字符串转数字
    template<typename T>
    inline T Str2Digit(std::string src)
    {
        std::stringstream ss;
        ss << src;
        T dest;
        ss >> dest;
        return dest;
    }

    inline bool SplitString(const char* src, const char* delim, std::vector<std::string>& dest)
    {
        if (src == NULL)
            return false;
        dest.clear();
        std::string remain(src);
        char* pSrc = &remain[0];
        char* pChild = NULL;
        pChild = strtok(pSrc, delim);
        if (pChild)
        {
            dest.push_back(pChild);
            while ((pChild = strtok(nullptr, delim)) != NULL)
            {
                dest.push_back(pChild);
            }
        }
        return !dest.empty();
    }

    inline bool SplitStringInt(const char* src, const char* delim, std::vector<int32_t>& dest)
    {
        if (src == NULL)
            return false;
        dest.clear();
        std::string remain(src);
        char* pSrc = &remain[0];
        char* pChild = NULL;
        pChild = strtok(pSrc, delim);
        if (pChild)
        {
            dest.push_back(atoi(pChild));
            while ((pChild = strtok(nullptr, delim)) != NULL)
            {
                dest.push_back(std::stol(pChild));
            }
        }
        return !dest.empty();
    }

    inline bool SplitStringInt(const char* src, const char *delim, std::vector<uint32_t>& dest)
    {
        if (src == NULL)
            return false;
        dest.clear();
        std::string remain(src);
        char* pSrc = &remain[0];
        char* pChild = NULL;
        pChild = strtok(pSrc, delim);
        if (pChild)
        {
            dest.push_back(atoi(pChild));
            while ((pChild = strtok(nullptr, delim)) != NULL)
            {
                dest.push_back(std::stoul(pChild));
            }
        }
        return !dest.empty();
    }

    inline bool SplitStringFloat(const char* src, const char *delim, std::vector<float>& dest)
    {
        if (src == NULL)
            return false;
        dest.clear();
        std::string remain(src);
        char* pSrc = &remain[0];
        char* pChild = NULL;
        pChild = strtok(pSrc, delim);
        if (pChild)
        {
            dest.push_back(atoi(pChild));
            while ((pChild = strtok(nullptr, delim)) != NULL)
            {
                dest.push_back(std::stof(pChild));
            }
        }

        return !dest.empty();
    }

    template<typename T>
    inline bool SplitIdCount(const char* src, const char *dlim1, const char *dlim2, std::vector<T>& dest)
    {
        std::vector<std::string> vs;
        SplitString(src, dlim1, vs);
        if (vs.size() > 0)
        {
            for (auto s : vs)
            {
                if (s.size() > 0)
                {
                    std::vector<int32_t> vi;
                    SplitStringInt(s.c_str(), dlim2, vi);
                    if (vi.size() == 2)
                    {
                        dest.push_back(
                        { static_cast<uint32_t>(vi[0]), static_cast<uint32_t>(vi[1]) });
                    }
                }
            }
        }
        return !dest.empty();
    }

    inline int32_t GetDayNum(time_t nTime)
    {
        struct tm tmTime;
        localtime_r(&nTime, &tmTime);
        int32_t nCurYear = tmTime.tm_year + 1900;
        int32_t nValue = nCurYear * 365 + nCurYear / 4 - nCurYear / 100 + nCurYear / 400;
        return nValue;
    }

    inline bool IsSameDay(time_t t1, time_t t2)
    {
        struct tm tm1, tm2;
        localtime_r(&t1, &tm1);
        localtime_r(&t2, &tm2);
        return tm1.tm_mday == tm2.tm_mday && tm1.tm_mon == tm2.tm_mon && tm1.tm_year == tm2.tm_year;
    }
}

#endif /* FUNC_H_ */
